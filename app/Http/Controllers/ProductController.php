<?php

namespace App\Http\Controllers;

use App\Components\Upload\UploadHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use App\Components\Traits\UploadFirebase;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Product;

class ProductController extends Controller
{
    use UploadFirebase;

    public function index()
    {
        $products = Product::query()
            ->when(request()->cooperative, function ($query, $cooperative) {
                $query->where('cooperative_id', '=', $cooperative);
            })->when(request()->name, function ($query, $name) {
                $query->where('name', 'like', "%$name%");
            })->when(request()->category, function ($query, $category) {
                $query->where('category_id', '=', $category);
            })->when((request()->max_price || request()->min_price), function ($query, $value) {
                $max = request()->input('max_price', PHP_INT_MAX);
                $min = request()->input('min_price', 0);
                $query->whereBetween('price', [$min, $max]);
            })->when(request()->order, function ($query, $order) {
                $order = $order == 'asc' || $order == 'desc' ? $order : 'asc';
                $query->reorder('price', $order);
            })->paginate(100);

        return response()->json($products);
    }

    public function store(StoreRequest $request, UploadHandler $uploader)
    {
        $user = $request->user();
        $validatedData = $request->validated();

        $product = new Product();
        $product->cooperative_id = $user->cooperative_id;
        $product->fill($validatedData);
        $product->photo_path = $uploader->upload($validatedData['photo_path']);

        $product->save();

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(UpdateRequest $request, Product $product, UploadHandler $uploader)
    {
        $product->fill($request->all());
        if ($request->hasFile('photo_path') && ($photo = $request->file('photo_path'))) {
            $product->photo_path = $uploader->upload($photo);
        }
        $product->save();

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        if (Gate::denies('manage-product', $product)) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $product->delete();

        return response()->json(null, 204);
    }
}
