<?php

namespace Cms\Http\Controllers;

use Cms\Models\Product;
use Cms\Support\CmsAuth;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductsController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $query = Product::query()->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        return view('cms::products.index', [
            'products' => $query->get(),
            'search' => $search,
            'canEdit' => CmsAuth::canEdit(),
        ]);
    }
}
