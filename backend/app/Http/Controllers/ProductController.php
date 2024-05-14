<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


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
    public function show(Product $product)
    {
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
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
        }

        $product->update($validated);
        return response()->json($product);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $productImage = $product->image;
        $deleted = $product->delete();

        if ($deleted === false) return response()->json(['message' => 'Erro ao excluir o produto'], 400);

        if ($productImage && Storage::disk('public')->exists($productImage)) {
            Storage::disk('public')->delete($productImage);
        }

        return response()->json(['message' => 'Produto excluído com sucesso'], 200);
    }
}
