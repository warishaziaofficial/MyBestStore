<?php

namespace Scripts\Lib;

use Illuminate\Support\Str;

class DigitalwaresImporter
{
    private const BASE_URL = 'https://digitalwares.pk';

    private const LOGO_URL = 'https://digitalwares.pk/data/logo/latest-logo.png';

    private ImportLogger $logger;

    private CatalogWriter $writer;

    private bool $dryRun;

    private bool $skipImages;

    private int $limit;

    /** @var array<string, array{name: string, slug: string, id?: int}> */
    private array $categories = [];

    /** @var array<string, true> */
    private array $productSlugs = [];

    public function __construct(ImportLogger $logger, bool $dryRun = false, bool $skipImages = false, int $limit = 0)
    {
        $this->logger = $logger;
        $this->writer = CatalogWriterFactory::make();
        $this->dryRun = $dryRun;
        $this->skipImages = $skipImages;
        $this->limit = max(0, $limit);
    }

    /** @return array<string, int> */
    public function run(): array
    {
        $this->logger->log('created', ($this->dryRun ? 'DRY RUN — ' : '').'Import using '.$this->writer->mode().' catalog tables');

        $this->discoverCategories();
        $this->importLogo();
        $this->importCategories();
        $this->discoverProducts();
        $this->importProducts();

        if (! $this->dryRun && $this->writer->mode() === 'laravel') {
            CatalogJsonExporter::exportFromLaravel();
            CatalogJsonExporter::ensureStorefrontFallback();
            $this->logger->log('created', 'Updated scripts/generated-catalog.json and enabled STOREFRONT_DEV_FALLBACK');
        }

        return $this->logger->counts();
    }

    public static function backupDatabase(): ?string
    {
        $source = database_path('database.sqlite');

        if (! is_file($source)) {
            return null;
        }

        $backupDir = database_path('backups');

        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $destination = $backupDir.'/database-'.date('Y-m-d-His').'.sqlite';

        if (! copy($source, $destination)) {
            throw new \RuntimeException('Failed to copy database to '.$destination);
        }

        return $destination;
    }

    private function discoverCategories(): void
    {
        $html = $this->fetch(self::BASE_URL.'/shop');

        if ($html === null) {
            $this->logger->log('failed', 'Could not fetch shop page for categories');

            return;
        }

        if (preg_match_all('#/category/([a-z0-9\-]+)\.html[^>]*>([^<]+)</a>#i', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $slug = strtolower(trim($match[1]));
                $name = html_entity_decode(trim($match[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8');

                if ($slug === '' || $name === '') {
                    continue;
                }

                $this->categories[$slug] = ['name' => $name, 'slug' => $slug];
            }
        }

        $this->logger->log('created', 'Discovered '.count($this->categories).' categories');
    }

    private function discoverProducts(): void
    {
        $page = 1;

        while ($page <= 50) {
            $url = $page === 1 ? self::BASE_URL.'/shop' : self::BASE_URL.'/shop?page='.$page;
            $html = $this->fetch($url);

            if ($html === null) {
                $this->logger->log('failed', "Could not fetch shop page {$page}");
                break;
            }

            $foundOnPage = 0;

            if (preg_match_all('#/product/([a-z0-9\-]+)/s1\.html#i', $html, $matches)) {
                foreach ($matches[1] as $slug) {
                    $slug = strtolower(trim($slug));

                    if ($slug !== '' && ! isset($this->productSlugs[$slug])) {
                        $this->productSlugs[$slug] = true;
                        $foundOnPage++;
                    }
                }
            }

            if ($foundOnPage === 0) {
                break;
            }

            if (! preg_match('/of\s+(\d+)\s+items/i', $html, $totalMatch)) {
                $page++;

                continue;
            }

            $total = (int) $totalMatch[1];
            $expectedPages = (int) ceil($total / max($foundOnPage, 12));

            if ($page >= $expectedPages) {
                break;
            }

            $page++;
        }

        $this->logger->log('created', 'Discovered '.count($this->productSlugs).' product slugs');
    }

    private function importLogo(): void
    {
        if ($this->skipImages) {
            $this->logger->log('skipped', 'Logo download skipped (--skip-images)');

            return;
        }

        $relativeUpload = 'uploads/cms/import/'.date('Y/m').'/digitalwares-logo.png';
        $absoluteUpload = public_path($relativeUpload);
        $publicLogo = public_path('logo.png');

        if ($this->dryRun) {
            $this->logger->log('created', 'Would download logo to '.$relativeUpload.' and public/logo.png');

            return;
        }

        if (! is_dir(dirname($absoluteUpload))) {
            mkdir(dirname($absoluteUpload), 0755, true);
        }

        if (is_file($publicLogo)) {
            $backupLogo = database_path('backups/logo-'.date('Y-m-d-His').'.png');

            if (! is_dir(dirname($backupLogo))) {
                mkdir(dirname($backupLogo), 0755, true);
            }

            copy($publicLogo, $backupLogo);
            $this->logger->log('created', 'Backed up existing logo.png');
        }

        if (! $this->downloadFile(self::LOGO_URL, $absoluteUpload)) {
            $this->logger->log('failed', 'Logo download failed from '.self::LOGO_URL);

            return;
        }

        copy($absoluteUpload, $publicLogo);
        $this->logger->log('created', 'Logo imported to public/logo.png');
    }

    private function importCategories(): void
    {
        foreach ($this->categories as $category) {
            $existing = $this->writer->findCategory($category['slug'], $category['name']);

            if ($this->dryRun) {
                $this->logger->log($existing ? 'updated' : 'created', 'Category [dry-run] '.($existing ? 'update' : 'create').": {$category['name']} ({$category['slug']})");

                continue;
            }

            $saved = $this->writer->upsertCategory([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'description' => 'Imported from digitalwares.pk — '.$category['name'].'.',
                'image' => $existing?->image ?? 'placeholder-product.svg',
            ], $existing);

            $this->categories[$category['slug']]['id'] = $saved->id;
            $this->logger->log($existing ? 'updated' : 'created', 'Category '.($existing ? 'updated' : 'created').": {$category['name']}");
        }
    }

    private function importProducts(): void
    {
        $slugs = array_keys($this->productSlugs);

        if ($this->limit > 0) {
            $slugs = array_slice($slugs, 0, $this->limit);
        }

        foreach ($slugs as $index => $slug) {
            try {
                $this->importProduct($slug);
            } catch (\Throwable $e) {
                $this->logger->log('failed', "Product {$slug}: ".$e->getMessage());
            }

            if (($index + 1) % 10 === 0) {
                usleep(250000);
            }
        }

        if (! $this->dryRun) {
            $this->writer->refreshCategoryCounts();
        }
    }

    private function importProduct(string $slug): void
    {
        $html = $this->fetch(self::BASE_URL.'/product/'.$slug.'/s1.html');

        if ($html === null) {
            $this->logger->log('failed', "Could not fetch product page: {$slug}");

            return;
        }

        $parsed = $this->parseProductPage($html, $slug);

        if ($parsed['name'] === '') {
            $this->logger->log('failed', "Could not parse product name: {$slug}");

            return;
        }

        $existing = $this->writer->findProduct($parsed['slug'], $parsed['name'], $parsed['sku']);

        if ($this->dryRun) {
            $this->logger->log($existing ? 'updated' : 'created', "Product [dry-run] ".($existing ? 'update' : 'create').": {$parsed['name']} — Rs.{$parsed['price']}, stock {$parsed['stock']}");

            return;
        }

        $this->ensureCategoryExists($parsed);

        $imagePath = $existing?->image ?? 'placeholder-product.svg';

        if (! $this->skipImages && $parsed['primary_image'] !== '') {
            $imagePath = $this->downloadProductImage($parsed['primary_image'], $parsed['slug']) ?? $imagePath;
        }

        $categoryId = $this->categories[$parsed['category_slug']]['id'] ?? null;

        $product = $this->writer->upsertProduct([
            'category_id' => $categoryId,
            'name' => $parsed['name'],
            'slug' => $parsed['slug'],
            'sku' => $parsed['sku'],
            'description' => $this->buildDescription($parsed),
            'price' => $parsed['price'],
            'image' => $imagePath,
            'category_slug' => $parsed['category_slug'],
            'sub_category' => $parsed['sub_category'],
            'brand' => $parsed['brand'],
            'stock' => $parsed['stock'],
        ], $existing);

        if (! $this->skipImages && $parsed['gallery_images'] !== []) {
            $gallery = [];
            $sort = 0;

            foreach ($parsed['gallery_images'] as $imageUrl) {
                $local = $this->downloadProductImage($imageUrl, $parsed['slug'].'-'.$sort);

                if ($local === null) {
                    continue;
                }

                $gallery[] = [
                    'path' => $local,
                    'alt' => $parsed['name'],
                    'sort_order' => $sort,
                    'is_primary' => $sort === 0,
                ];
                $sort++;
            }

            if ($gallery !== []) {
                $this->writer->syncProductImages($product, $gallery);
            }
        }

        $this->logger->log($existing ? 'updated' : 'created', 'Product '.($existing ? 'updated' : 'created').": {$parsed['name']} (#{$product->id})");
    }

    /** @param array<string, mixed> $parsed */
    private function ensureCategoryExists(array $parsed): void
    {
        if ($parsed['category_slug'] === '') {
            return;
        }

        if (isset($this->categories[$parsed['category_slug']]['id'])) {
            return;
        }

        $name = $parsed['category_name'] ?: Str::title(str_replace('-', ' ', $parsed['category_slug']));
        $existing = $this->writer->findCategory($parsed['category_slug'], $name);
        $saved = $this->writer->upsertCategory([
            'name' => $name,
            'slug' => $parsed['category_slug'],
            'description' => 'Imported from digitalwares.pk.',
            'image' => 'placeholder-product.svg',
        ], $existing);

        $this->categories[$parsed['category_slug']] = [
            'name' => $name,
            'slug' => $parsed['category_slug'],
            'id' => $saved->id,
        ];
    }

    /** @return array{name: string, slug: string, sku: string, price: int, stock: int, category_slug: string, category_name: string, sub_category: string, brand: string, description_html: string, primary_image: string, gallery_images: list<string>} */
    private function parseProductPage(string $html, string $slug): array
    {
        $name = '';

        if (preg_match('/<form[^>]*class="product-information"[\s\S]*?<h2>\s*([^<]+?)\s*<\/h2>/i', $html, $match)
            || preg_match('/<div class="product-right">[\s\S]*?<h2>\s*([^<]+?)\s*<\/h2>/i', $html, $match)) {
            $name = html_entity_decode(trim($match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        if (($name === '' || strcasecmp($name, 'product detail') === 0) && preg_match('/<title>([^<]+)<\/title>/i', $html, $match)) {
            $name = html_entity_decode(trim($match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $sku = preg_match('/SKU:\s*<span>([^<]+)<\/span>/i', $html, $match) ? trim($match[1]) : '';

        $price = 0;

        if (preg_match('/<div class="product-right">[\s\S]*?Rs\.?\s*([\d,]+)/i', $html, $match)) {
            $price = (int) str_replace(',', '', $match[1]);
        }

        $stock = 0;

        if (preg_match('/Stock status:[\s\S]*?<span[^>]*>\s*([^<]+?)\s*<\/span>/i', $html, $match)) {
            $stockText = strtolower(trim(html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            $stock = str_contains($stockText, 'in stock') ? 10 : 0;
        }

        $categorySlug = '';
        $categoryName = '';

        if (preg_match('/Category:[\s\S]*?\/category\/([a-z0-9\-]+)\.html[^>]*>([^<]+)</i', $html, $match)) {
            $categorySlug = strtolower(trim($match[1]));
            $categoryName = html_entity_decode(trim(rtrim($match[2], ', ')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $descriptionHtml = '';

        if (preg_match('/id="top-home"[^>]*>([\s\S]*?)<\/div>\s*<div class="tab-pane/s', $html, $match)
            || preg_match('/id="top-home"[^>]*>([\s\S]*?)<\/div>\s*<\/div>\s*<\/div>/s', $html, $match)) {
            $descriptionHtml = trim($match[1]);
        }

        $primaryImage = preg_match('/property="og:image"\s+content="([^"]+)"/i', $html, $match) ? trim($match[1]) : '';

        $gallery = [];

        if (preg_match_all('#https?://[^"\']+/data/product/[^"\']+#i', $html, $imageMatches)) {
            foreach ($imageMatches[0] as $imageUrl) {
                $imageUrl = $this->normalizeImageUrl($imageUrl);

                if (! str_contains($imageUrl, '/thumbs/')) {
                    $gallery[] = $imageUrl;
                }
            }
        }

        if ($primaryImage !== '') {
            array_unshift($gallery, $this->normalizeImageUrl($primaryImage));
        }

        $gallery = array_values(array_unique(array_filter($gallery)));

        if ($primaryImage === '' && $gallery !== []) {
            $primaryImage = $gallery[0];
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'sku' => $sku,
            'price' => $price,
            'stock' => $stock,
            'category_slug' => $categorySlug,
            'category_name' => $categoryName,
            'sub_category' => $categoryName,
            'brand' => $this->guessBrand($name),
            'description_html' => $descriptionHtml,
            'primary_image' => $primaryImage,
            'gallery_images' => $gallery,
        ];
    }

    /** @param array<string, mixed> $parsed */
    private function buildDescription(array $parsed): string
    {
        $parts = [];

        if (! empty($parsed['sku']) && $this->writer->mode() === 'cms') {
            $parts[] = '<p><strong>SKU:</strong> '.htmlspecialchars($parsed['sku'], ENT_QUOTES, 'UTF-8').'</p>';
        }

        if ($parsed['price'] <= 0) {
            $parts[] = '<p><em>Price not confirmed — contact us for a quote.</em></p>';
        }

        if (! empty($parsed['description_html'])) {
            $parts[] = $parsed['description_html'];
        }

        return implode("\n", $parts);
    }

    private function downloadProductImage(string $url, string $basename): ?string
    {
        $url = $this->normalizeImageUrl($url);
        $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg';
        $extension = strtolower(preg_replace('/[^a-z0-9]/', '', $extension) ?: 'jpg');
        $safeBase = Str::slug($basename) ?: 'product';
        $directory = 'uploads/cms/'.date('Y/m');
        $absoluteDirectory = public_path($directory);

        if (! is_dir($absoluteDirectory)) {
            mkdir($absoluteDirectory, 0755, true);
        }

        $filename = $safeBase.'.'.$extension;
        $absolutePath = $absoluteDirectory.'/'.$filename;
        $relativePath = $directory.'/'.$filename;

        if (is_file($absolutePath) && filesize($absolutePath) > 500) {
            return $relativePath;
        }

        if (! $this->downloadFile($url, $absolutePath)) {
            $fallback = preg_replace('#/thumbs/#', '/', $url);
            $fallback = preg_replace('#-\d+\.(jpe?g|png|webp)$#i', '.$1', $fallback);

            if ($fallback === $url || ! $this->downloadFile($fallback, $absolutePath)) {
                return null;
            }
        }

        return is_file($absolutePath) ? $relativePath : null;
    }

    private function downloadFile(string $url, string $destination): bool
    {
        $data = $this->fetch($url, true);

        if ($data === null || strlen($data) < 200) {
            return false;
        }

        return file_put_contents($destination, $data) !== false;
    }

    private function fetch(string $url, bool $binary = false): ?string
    {
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: MyBestStore-Import/1.0\r\nAccept: */*\r\n",
                    'timeout' => 45,
                    'ignore_errors' => true,
                ],
                'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
            ]);

            $body = @file_get_contents($url, $binary, $context);

            if ($body !== false && ($binary || strlen($body) > 200)) {
                return $body;
            }

            usleep(500000 * $attempt);
        }

        return null;
    }

    private function normalizeImageUrl(string $url): string
    {
        $url = html_entity_decode(trim($url), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (str_starts_with($url, '//')) {
            return 'https:'.$url;
        }

        if (str_starts_with($url, '/')) {
            return self::BASE_URL.$url;
        }

        return $url;
    }

    private function guessBrand(string $name): string
    {
        foreach (['Zebra', 'HP', 'Dell', 'TSC', 'Epson', 'Honeywell', 'Hikvision', 'Dahua', 'Canon', 'Brother', 'Microsoft'] as $brand) {
            if (stripos($name, $brand) !== false) {
                return $brand;
            }
        }

        return '';
    }
}
