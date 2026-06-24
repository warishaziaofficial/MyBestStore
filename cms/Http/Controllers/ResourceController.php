<?php

namespace Cms\Http\Controllers;

use Cms\Models\Media;
use Cms\Support\CmsAuth;
use Cms\Support\FormOptions;
use Cms\Support\MediaStorage;
use Cms\Support\ModuleMeta;
use Cms\Support\ProductRelationSync;
use Cms\Support\ResourceRegistry;
use Cms\Support\StockAlertNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(Request $request, string $entity): View
    {
        $config = ResourceRegistry::get($entity);
        $this->authorizeAccess($config);

        $model = $config['model'];
        $meta = ModuleMeta::for($entity);
        $direction = ($config['list_order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $query = $model::query();
        $search = trim((string) $request->query('search', ''));

        if ($search !== '' && $meta['search_columns'] !== []) {
            $query->where(function ($builder) use ($search, $meta): void {
                foreach ($meta['search_columns'] as $column) {
                    $builder->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        $items = $query->orderBy('id', $direction)->paginate(20)->withQueryString();

        if ($entity === 'media') {
            return view('cms::media.index', [
                'entity' => $entity,
                'config' => $config,
                'items' => $items,
                'canEdit' => CmsAuth::canEdit(),
            ]);
        }

        return view('cms::crud.index', [
            'entity' => $entity,
            'config' => $config,
            'meta' => $meta,
            'items' => $items,
            'search' => $search,
            'canEdit' => CmsAuth::canEdit(),
        ]);
    }

    public function create(string $entity): View
    {
        $config = ResourceRegistry::get($entity);
        $this->authorizeAccess($config);
        $this->requireEditor();
        $this->requireWritable($config);

        if ($entity === 'products') {
            return view('cms::products.form', [
                'entity' => $entity,
                'config' => $config,
                'item' => null,
                'formOptions' => FormOptions::forConfig($config['fields']),
                'allProducts' => \Cms\Models\Product::query()->orderBy('name')->get(['id', 'name']),
                'productRelations' => ProductRelationSync::TYPES,
                'selectedRelations' => array_fill_keys(array_keys(ProductRelationSync::TYPES), []),
                'galleryImages' => collect(),
            ]);
        }

        return view('cms::crud.form', [
            'entity' => $entity,
            'config' => $config,
            'item' => null,
            'formOptions' => FormOptions::forConfig($config['fields']),
        ]);
    }

    public function store(Request $request, string $entity): RedirectResponse
    {
        $config = ResourceRegistry::get($entity);
        $this->authorizeAccess($config);
        $this->requireEditor();
        $this->requireWritable($config);

        if ($entity === 'media') {
            return $this->storeMedia($request);
        }

        $data = $this->validatedData($request, $config);
        $model = $config['model'];

        if ($model === \Cms\Models\Product::class) {
            $data['rating'] = $data['rating'] ?? 0;
            $data['review_count'] = $data['review_count'] ?? 0;
            $data['stock'] = $data['stock'] ?? 0;

            if (! Schema::hasColumn('Products', 'cost_price')) {
                unset($data['cost_price']);
            }

            if (! Schema::hasColumn('Products', 'description')) {
                unset($data['description']);
            }
        }

        if ($model === \Cms\Models\Testimonial::class) {
            $data['is_featured'] = $data['is_featured'] ?? 1;
            $data['sort_order'] = $data['sort_order'] ?? 0;
        }

        if (in_array($model, [
            \Cms\Models\PromoBanner::class,
            \Cms\Models\FeaturedCollection::class,
            \Cms\Models\ContactCard::class,
            \Cms\Models\TrustItem::class,
        ], true)) {
            $data['is_active'] = $data['is_active'] ?? 1;
            $data['sort_order'] = $data['sort_order'] ?? 0;
        }

        if ($model === \Cms\Models\ProductImage::class) {
            $data['sort_order'] = $data['sort_order'] ?? 0;
        }

        if ($model === \Cms\Models\StaticPage::class) {
            $data['is_published'] = $data['is_published'] ?? 1;
        }

        if ($model === \Cms\Models\EmailTemplate::class) {
            $data['is_active'] = $data['is_active'] ?? 1;
        }

        if ($model === \Cms\Models\Category::class) {
            $data = $this->applyCategoryData($data);
        }

        if ($model === \Cms\Models\Brand::class || $entity === 'brands') {
            $data = $this->applyBrandData($data);
        }

        $created = $model::create($data);

        if ($model === \Cms\Models\Product::class) {
            ProductRelationSync::sync((int) $created->id, $request->input('relations', []));
            \Cms\Support\ProductGallerySync::sync((int) $created->id, $request);
        }

        return redirect()->route('cms.products.index')->with('success', $config['singular'].' created.');
    }

    public function edit(string $entity, string $id): View
    {
        $config = ResourceRegistry::get($entity);
        $this->authorizeAccess($config);
        $this->requireEditor();
        $this->requireWritable($config);

        $item = $this->findItem($config, $id);

        if ($entity === 'products') {
            return view('cms::products.form', [
                'entity' => $entity,
                'config' => $config,
                'item' => $item,
                'formOptions' => FormOptions::forConfig($config['fields']),
                'allProducts' => \Cms\Models\Product::query()->where('id', '!=', $item->id)->orderBy('name')->get(['id', 'name']),
                'productRelations' => ProductRelationSync::TYPES,
                'selectedRelations' => ProductRelationSync::groupedForProduct((int) $item->id),
                'galleryImages' => \Cms\Support\ProductGallerySync::forProduct((int) $item->id),
            ]);
        }

        return view('cms::crud.form', [
            'entity' => $entity,
            'config' => $config,
            'item' => $item,
            'formOptions' => FormOptions::forConfig($config['fields']),
        ]);
    }

    public function update(Request $request, string $entity, string $id): RedirectResponse
    {
        $config = ResourceRegistry::get($entity);
        $this->authorizeAccess($config);
        $this->requireEditor();
        $this->requireWritable($config);

        if ($entity === 'media') {
            $item = $this->findItem($config, $id);
            $data = $request->validate([
                'alt_text' => ['nullable', 'string', 'max:255'],
            ]);
            $item->update($data);

            return redirect()->route('cms.resource.index', $entity)->with('success', 'Media file updated.');
        }

        $item = $this->findItem($config, $id);
        $previousStock = ($config['model'] === \Cms\Models\Product::class && isset($item->stock))
            ? (int) $item->stock
            : null;
        $data = $this->validatedData($request, $config, $item);

        if ($config['model'] === \Cms\Models\Product::class && ! Schema::hasColumn('Products', 'cost_price')) {
            unset($data['cost_price']);
        }

        if ($config['model'] === \Cms\Models\Product::class && ! Schema::hasColumn('Products', 'description')) {
            unset($data['description']);
        }

        if ($config['model'] === \Cms\Models\Category::class) {
            $data = $this->applyCategoryData($data, $item);
        }

        $item->update($data);

        if ($config['model'] === \Cms\Models\Product::class) {
            ProductRelationSync::sync((int) $item->id, $request->input('relations', []));

            if ($previousStock !== null && array_key_exists('stock', $data)) {
                StockAlertNotifier::afterStockChange((int) $item->id, $previousStock);
            }

            \Cms\Support\ProductGallerySync::sync((int) $item->id, $request);
        }

        return redirect()->route('cms.products.index')->with('success', $config['singular'].' updated.');
    }

    public function destroy(string $entity, string $id): RedirectResponse
    {
        $config = ResourceRegistry::get($entity);
        $this->authorizeAccess($config);
        $this->requireEditor();
        $this->requireWritable($config);

        $item = $this->findItem($config, $id);

        if ($config['model'] === Media::class) {
            MediaStorage::deleteFile($item->path);
        }

        $item->delete();

        return redirect()->route('cms.resource.index', $entity)->with('success', $config['singular'].' deleted.');
    }

    private function storeMedia(Request $request): RedirectResponse
    {
        $request->validate([
            'upload' => ['required', 'file', 'image', 'max:5120'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $stored = MediaStorage::store($request->file('upload'));
        Media::create(array_merge($stored, [
            'alt_text' => $request->input('alt_text'),
        ]));

        return redirect()->route('cms.resource.index', 'media')->with('success', 'Media File uploaded.');
    }

    private function authorizeAccess(array $config): void
    {
        if (! empty($config['admin_only']) && ! CmsAuth::isAdmin()) {
            abort(403);
        }
    }

    private function requireWritable(array $config): void
    {
        if (! empty($config['read_only'])) {
            abort(403, 'This resource is read-only.');
        }
    }

    private function requireEditor(): void
    {
        if (! CmsAuth::canEdit()) {
            abort(403);
        }
    }

    private function findItem(array $config, string $id)
    {
        $key = $config['key'] ?? 'id';

        return $config['model']::where($key, $id)->firstOrFail();
    }

    private function validatedData(Request $request, array $config, $item = null): array
    {
        $rules = [];
        $isUpdate = $item !== null;

        foreach ($config['fields'] as $name => $field) {
            if (! empty($field['virtual']) || ! empty($field['hidden'])) {
                continue;
            }

            if ($isUpdate && ! empty($field['create'])) {
                continue;
            }

            if (! $isUpdate && ($field['edit'] ?? true) === false && empty($field['virtual'])) {
                continue;
            }

            if ($isUpdate && ($field['edit'] ?? true) === false) {
                continue;
            }

            if ($isUpdate && ($field['type'] ?? '') === 'password' && ! $request->filled($name)) {
                continue;
            }

            $fieldType = $field['type'] ?? 'text';

            if ($fieldType === 'image') {
                $rules[$name] = $isUpdate ? ['nullable', 'string', 'max:500'] : ['nullable', 'string', 'max:500'];
                $rules[$name.'_file'] = $name === 'logo'
                    ? ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp,svg', 'max:5120']
                    : ['nullable', 'file', 'image', 'max:5120'];

                if ($field['required'] && ! $isUpdate) {
                    $rules[$name][] = 'required_without:'.$name.'_file';
                }

                continue;
            }

            $rule = [];

            if ($field['required'] && ! $isUpdate && $fieldType !== 'password') {
                $rule[] = 'required';
            } elseif ($field['required'] && ! $isUpdate && $fieldType === 'password') {
                $rule[] = 'required';
            } elseif ($fieldType === 'password' && $isUpdate) {
                $rule[] = 'nullable';
            } elseif ($field['required']) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }

            if ($fieldType === 'email') {
                $rule[] = 'email';
            }

            if ($fieldType === 'number' || ($fieldType === 'relation_select' && ($field['source'] ?? '') === 'products')) {
                $rule[] = 'numeric';
            }

            if ($name === 'email' && $config['model'] === \Cms\Models\User::class) {
                $rule[] = 'unique:Users,email'.($isUpdate ? ','.$item->id : '');
            }

            if ($name === 'email' && $config['model'] === \Cms\Models\Customer::class) {
                $rule[] = 'unique:Customers,email'.($isUpdate ? ','.$item->id : '');
            }

            if ($name === 'email' && $config['model'] === \Cms\Models\NewsletterSubscriber::class) {
                $rule[] = 'unique:NewsletterSubscribers,email'.($isUpdate ? ','.$item->id : '');
            }

            if ($name === 'order_number' && $config['model'] === \Cms\Models\Order::class) {
                $rule[] = 'unique:Orders,order_number'.($isUpdate ? ','.$item->id : '');
            }

            if (in_array($name, ['product_id'], true)) {
                $rule[] = 'exists:Products,id';
            }

            if (in_array($name, ['slug'], true)) {
                $table = (new $config['model'])->getTable();
                $rule[] = 'unique:'.$table.','.$name.($isUpdate ? ','.$item->id : '');
            }

            $rules[$name] = $rule;
        }

        $data = $request->validate($rules);

        foreach ($config['fields'] as $name => $field) {
            if (! empty($field['virtual'])) {
                continue;
            }

            $fieldType = $field['type'] ?? 'text';

            if ($fieldType === 'image') {
                if ($request->hasFile($name.'_file')) {
                    $stored = MediaStorage::store($request->file($name.'_file'));
                    Media::create(array_merge($stored, [
                        'alt_text' => $request->input($name.'_alt') ?: $request->input('image_alt'),
                    ]));
                    $data[$name] = $stored['path'];
                } elseif ($request->boolean('remove_image')) {
                    $data[$name] = '';
                } elseif (empty($data[$name] ?? null) && $item) {
                    $data[$name] = $item->{$name};
                }

                unset($data[$name.'_file']);

                continue;
            }

            if ($fieldType === 'checkbox') {
                $data[$name] = $request->boolean($name) ? 1 : 0;
            }

            if (! empty($field['hash']) && ! empty($data[$name])) {
                $data[$name] = Hash::make($data[$name]);
            }

            if ($fieldType === 'password' && empty($data[$name] ?? null)) {
                unset($data[$name]);
            }

            if ($fieldType === 'select' && ($data[$name] ?? '') === '') {
                $data[$name] = null;
            }

            if ($fieldType === 'relation_select' && ($field['source'] ?? '') === 'products' && isset($data[$name])) {
                $data[$name] = (int) $data[$name];
            }
        }

        foreach ($config['fields'] as $name => $field) {
            if (($field['type'] ?? '') !== 'image' || ! empty($field['virtual'])) {
                continue;
            }

            $path = $data[$name] ?? null;

            if ($path && ! MediaStorage::isRemoteUrl($path) && ! MediaStorage::exists($path)) {
                throw ValidationException::withMessages([
                    $name => 'Image file is missing on the server. Please upload the image again (do not only paste a path).',
                ]);
            }
        }

        return $data;
    }

    private function applyBrandData(array $data, $existing = null): array
    {
        if ($existing) {
            return $data;
        }

        $id = Str::slug((string) ($data['name'] ?? ''));

        if ($id === '') {
            $id = 'brand-'.Str::lower(Str::random(8));
        }

        $base = $id;
        $suffix = 2;

        while (\Cms\Models\Brand::query()->where('id', $id)->exists()) {
            $id = $base.'-'.$suffix;
            $suffix++;
        }

        $data['id'] = $id;

        return $data;
    }

    private function applyCategoryData(array $data, $existing = null): array
    {
        $slug = trim((string) ($data['slug'] ?? ''));

        if ($slug === '' && $existing) {
            $slug = (string) $existing->slug;
        } elseif ($slug === '') {
            $slug = Str::slug((string) ($data['name'] ?? ''));
        }

        $base = $slug;
        $suffix = 2;

        while (\Cms\Models\Category::query()
            ->where('slug', $slug)
            ->when($existing, fn ($query) => $query->where('id', '!=', $existing->id))
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        $data['slug'] = $slug;

        if (trim((string) ($data['image_alt'] ?? '')) === '') {
            $data['image_alt'] = (string) ($data['name'] ?? '');
        }

        $data['description'] = trim((string) ($data['description'] ?? ''));

        $data['count'] = \Cms\Models\Product::query()->where('category', $slug)->count();

        return $data;
    }
}
