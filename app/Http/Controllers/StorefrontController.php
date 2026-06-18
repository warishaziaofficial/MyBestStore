<?php

namespace App\Http\Controllers;

use App\Support\Mbs;
use App\Support\StorefrontData;
use Illuminate\Contracts\View\View;

class StorefrontController extends Controller
{
    public function home(): View
    {
        return view('pages.home', StorefrontData::home());
    }

    public function shop(): View
    {
        $data = StorefrontData::shared();
        $data['products'] = Mbs::paginate($data['products']);

        return view('pages.shop', $data);
    }

    public function categories(): View
    {
        $data = StorefrontData::shared();
        $data['exploreCategories'] = StorefrontData::exploreCategories();
        $data['popularProducts'] = array_slice(StorefrontData::bestSelling(), 0, 8);

        return view('pages.categories', $data);
    }

    public function newArrivals(): View
    {
        $data = StorefrontData::shared();
        $data['products'] = Mbs::paginate(StorefrontData::newArrivals());

        return view('pages.new-arrivals', $data);
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
