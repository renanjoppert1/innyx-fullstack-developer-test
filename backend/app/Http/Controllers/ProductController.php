<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Product::paginate();

//        $data->getCollection()->transform(function ($product) {
//            $product->image_url = $product->image_url;
//            return $product;
//        });

        return response()->json($data);
    }

    public function uploadImage($image, $imageName): bool
    {
        try {
            $image->storeAs('public/products', $imageName);
            return true;
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $validated = $request->validated();

        $image = $request->file('image');
        $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();
        $imageUploaded = $this->uploadImage($image, $imageName);

        if ($imageUploaded === false) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to upload image.'
            ], 400);
        }

        $validated['image'] = 'products/' . $imageName;

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
