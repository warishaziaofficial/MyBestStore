<?php

namespace App\Services;

use App\Support\StorefrontData;

class SmartRecommendationService
{
    public function __construct(
        private readonly ProductSalesAnalytics $sales,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query, int $limit = 12): array
    {
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        $constraints = $this->parseConstraints($query);
        $needle = strtolower($constraints['text']);

        $candidates = [];

        foreach (StorefrontData::allProducts() as $product) {
            if (! $this->matchesConstraints($product, $constraints)) {
                continue;
            }

            if ($needle !== '' && ! $this->matchesText($product, $needle)) {
                continue;
            }

            $candidates[] = $product;
        }

        if ($candidates === [] && $needle !== '') {
            foreach (StorefrontData::allProducts() as $product) {
                if ($this->matchesText($product, $needle)) {
                    $candidates[] = $product;
                }
            }
        }

        $rank = $this->sales->salesRankMap();
        $needleWords = $needle === '' ? [] : preg_split('/\s+/', $needle) ?: [];

        usort($candidates, function (array $a, array $b) use ($rank, $needleWords) {
            $scoreA = $this->scoreProduct($a, $rank, $needleWords);
            $scoreB = $this->scoreProduct($b, $rank, $needleWords);

            return $scoreB <=> $scoreA;
        });

        return array_map(
            fn (array $product) => StorefrontData::enrichProduct($product),
            array_slice($candidates, 0, $limit)
        );
    }

    /**
     * @return array{text: string, min_price: int|null, max_price: int|null, category: string|null}
     */
    private function parseConstraints(string $query): array
    {
        $minPrice = null;
        $maxPrice = null;
        $category = null;
        $text = $query;

        if (preg_match('/(?:under|below|max|upto|up to)\s*(?:rs\.?\s*)?([\d,]+)\s*(?:k|000)?/i', $query, $matches)) {
            $maxPrice = $this->parseMoney($matches[1]);
            $text = trim(preg_replace('/(?:under|below|max|upto|up to)\s*(?:rs\.?\s*)?[\d,]+\s*(?:k|000)?/i', '', $text) ?? $text);
        }

        if (preg_match('/(?:above|over|from|min)\s*(?:rs\.?\s*)?([\d,]+)\s*(?:k|000)?/i', $query, $matches)) {
            $minPrice = $this->parseMoney($matches[1]);
            $text = trim(preg_replace('/(?:above|over|from|min)\s*(?:rs\.?\s*)?[\d,]+\s*(?:k|000)?/i', '', $text) ?? $text);
        }

        if (preg_match('/budget\s*(?:rs\.?\s*)?([\d,]+)\s*(?:k|000)?/i', $query, $matches)) {
            $maxPrice = $this->parseMoney($matches[1]);
            $text = trim(preg_replace('/budget\s*(?:rs\.?\s*)?[\d,]+\s*(?:k|000)?/i', '', $text) ?? $text);
        }

        $categoryMap = [
            'tv' => 'led-tvs',
            'television' => 'led-tvs',
            'soundbar' => 'sound-bars',
            'sound bar' => 'sound-bars',
            'headphone' => 'audio-equipment',
            'speaker' => 'sound-bars',
            'purifier' => 'air-purifiers',
            'gaming' => 'accessories',
            'accessory' => 'accessories',
            'charger' => 'mobile-accessories',
        ];

        $lower = strtolower($query);

        foreach ($categoryMap as $keyword => $slug) {
            if (str_contains($lower, $keyword)) {
                $category = $slug;
                break;
            }
        }

        return [
            'text' => trim(preg_replace('/\s+/', ' ', $text) ?? ''),
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'category' => $category,
        ];
    }

    private function parseMoney(string $raw): int
    {
        $value = (int) str_replace(',', '', $raw);

        if ($value > 0 && $value < 1000 && ! str_contains($raw, ',')) {
            return $value * 1000;
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $product
     * @param  array{text: string, min_price: int|null, max_price: int|null, category: string|null}  $constraints
     */
    private function matchesConstraints(array $product, array $constraints): bool
    {
        $price = (int) ($product['price'] ?? 0);

        if ($constraints['min_price'] !== null && $price < $constraints['min_price']) {
            return false;
        }

        if ($constraints['max_price'] !== null && $price > $constraints['max_price']) {
            return false;
        }

        if ($constraints['category'] !== null && ($product['category'] ?? '') !== $constraints['category']) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $product
     */
    private function matchesText(array $product, string $needle): bool
    {
        $categoryName = StorefrontData::categoryLabel($product['category'] ?? '');

        $haystack = strtolower(implode(' ', array_filter([
            $product['name'] ?? '',
            $product['slug'] ?? '',
            $product['category'] ?? '',
            $categoryName,
            $product['sub_category'] ?? '',
            $product['brand'] ?? '',
            $product['description'] ?? '',
        ])));

        return str_contains($haystack, $needle);
    }

    /**
     * @param  array<string, mixed>  $product
     * @param  array<int, int>  $rank
     * @param  list<string>  $needleWords
     */
    private function scoreProduct(array $product, array $rank, array $needleWords): int
    {
        $score = ((int) ($rank[(int) ($product['id'] ?? 0)] ?? 0)) * 10;
        $score += (int) round(((float) ($product['rating'] ?? 0)) * 20);
        $score += min(100, (int) ($product['review_count'] ?? 0));

        if (! empty($product['featured'])) {
            $score += 15;
        }

        $name = strtolower($product['name'] ?? '');

        foreach ($needleWords as $word) {
            if ($word !== '' && str_contains($name, $word)) {
                $score += 25;
            }
        }

        return $score;
    }
}
