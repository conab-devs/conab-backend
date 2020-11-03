<?php

namespace App\Http\Controllers;

use App\Cooperative;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function index(Cooperative $cooperative)
    {
        if ($cooperative && Gate::allows('list-products-by-cooperative', $cooperative)) {
            return Product::where('cooperative_id', $cooperative->id)->get();
        } else if ($cooperative) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        return response()->json(Product::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $product = new Product();
        $product->cooperative_id = $user->cooperative_id;

        if (Gate::denies('manage-product', $product)) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $request->validate([
            'name' => 'bail|required|max:255',
            'price' => 'required|numeric|between:0,99999999.99',
            'photo_path' => 'required|image',
            'estimated_delivery_time' => 'required|integer',
            'category_id' => 'required|exists:App\Category,id',
        ]);

        $product->fill($request->all());
        $product->photo_path = App::environment('production')
            ? $this->uploadFileOnFirebase($request->file('photo_path'))
            : $request->file('photo_path')->store('uploads');

        $product->save();

        return response($product, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        if (Gate::denies('manage-product', $product)) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $request->validate([
            'name' => 'bail|max:255',
            'price' => 'numeric|between:0,99999999.99',
            'photo_path' => 'image',
            'estimated_delivery_time' => 'integer',
            'category_id' => 'exists:App\Category,id',
        ]);

        $product->fill($request->all());
        $product->photo_path = App::environment('production')
            ? $this->uploadFileOnFirebase($request->file('photo_path'))
            : $request->file('photo_path')->store('uploads');

        $product->save();

        return response($product);
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
    }
}
