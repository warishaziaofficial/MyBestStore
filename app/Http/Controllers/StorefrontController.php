<?php

namespace App\Http\Controllers;

use App\Services\ProductCatalogService;
use App\Support\StorefrontData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function __construct(
        private readonly ProductCatalogService $catalog,
    ) {}

    public function home(): View
    {
        return view('pages.home', StorefrontData::home());
    }

    public function shop(Request $request): View
    {
        $catalog = $this->catalog->browse($request, 'shop');

        return view('pages.shop', array_merge(StorefrontData::shared(), $catalog, [
            'showNewArrivalsFilter' => true,
            'showFeaturedFilter' => true,
        ]));
    }

    public function categories(Request $request): View
    {
        $catalog = $this->catalog->browse($request, 'categories');

        return view('pages.categories', array_merge(StorefrontData::shared(), $catalog, [
            'exploreCategories' => StorefrontData::exploreCategories(),
            'showNewArrivalsFilter' => false,
            'showFeaturedFilter' => true,
        ]));
    }

    public function newArrivals(Request $request): View
    {
        $catalog = $this->catalog->browse($request, 'new-arrivals');

        return view('pages.new-arrivals', array_merge(StorefrontData::shared(), $catalog, [
            'showNewArrivalsFilter' => false,
            'showFeaturedFilter' => true,
        ]));
    }

    public function blog(): View
    {
        return view('pages.blog', StorefrontData::shared());
    }

    public function contact(): View
    {
        return view('pages.contact', StorefrontData::shared());
    }
}
