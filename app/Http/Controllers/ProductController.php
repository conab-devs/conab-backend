<?php

namespace App\Http\Controllers;

use App\Components\Traits\UploadFirebase;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Product;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

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
            })->when((request()->max_price || request()->min_price), function ($query, $value)  {
                $max = request()->input('max_price', PHP_INT_MAX);
                $min = request()->input('min_price', 0);
                $query->whereBetween('price', [$min, $max]);
            })->when(request()->order, function ($query, $order) {
                $order = $order == 'asc' || $order == 'desc' ? $order : 'asc';
                $query->reorder('price', $order);
            })->paginate(100);

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $user = $request->user();

        $product = new Product();
        $product->cooperative_id = $user->cooperative_id;
        $product->fill($request->all());
        $product->photo_path = App::environment('production')
            ? $this->uploadFileOnFirebase($request->file('photo_path'))
            : $request->file('photo_path')->store('uploads');

        $product->save();

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Product $product)
    {
        $product->fill($request->all());
        if ($request->hasFile('photo_path') && ($photo = $request->file('photo_path'))) {
            $product->photo_path = App::environment('production')
                ? $this->uploadFileOnFirebase($photo)
                : $photo->store('uploads');
        }
        $product->save();

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if (Gate::denies('manage-product', $product)) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $product->delete();

        return response()->json();
    }
}
