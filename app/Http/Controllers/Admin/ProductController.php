<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductPostRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function store(ProductPostRequest $request)
    {
        $validated = $request->validated();

        $product = Product::create($validated);

        if (!empty($validated['category_ids'])) {
            $product->categories()->attach($validated['category_ids']);
        }

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully: '.$product,
            'error' => []
        ]);
    }
}
