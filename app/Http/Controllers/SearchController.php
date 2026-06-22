<?php

namespace App\Http\Controllers;

use App\Support\StorefrontData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function products(Request $request): JsonResponse
    {
        $query = (string) $request->query('q', '');
        $products = StorefrontData::searchProducts($query, 3);

        return response()->json([
            'count' => count($products),
            'html' => view('components.search-results-grid', [
                'products' => $products,
            ])->render(),
        ]);
    }
}
