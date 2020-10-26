<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ProductController extends Controller
{
    public function index()
    {
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

        $request->validate([
            'name' => 'bail|required|max:255',
            'price' => 'required|numeric|between:0,99999999.99',
            'photo_path' => 'required|image',
            'estimated_delivery_time' => 'required|integer',
            'category_id' => 'required|exists:App\Category,id',
        ]);

        $product = new Product();
        $product->fill($request->all());
        $product->cooperative_id = $user->cooperative_id;
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
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
