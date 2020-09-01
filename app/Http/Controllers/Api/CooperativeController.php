<?php

namespace App\Http\Controllers\Api;

use App\Address;
use App\Cooperative;
use App\Http\Controllers\Controller;
use App\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CooperativeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cooperatives = Cooperative::with(['address', 'phones'])->get();

        return response()->json($cooperatives);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'bail|required|unique:cooperatives|max:100',
            'dap_path' => 'required|unique:cooperatives|max:100',
            'phones.*.number' => 'required|distinct|unique:phones,number|max:15',
            'city' => 'required|max:100',
            'street' => 'required|max:100',
            'neighborhood' => 'required|max:100',
            'number' => 'required|max:10',
        ]);

        $address = Address::create($request->only(['city', 'street', 'neighborhood', 'number']));

        $cooperative = new Cooperative();
        $cooperative->fill($request->all());
        $cooperative->address_id = $address->id;
        $cooperative->save();
        $cooperative->phones()->createMany($request->input('phones'));

        return response()->json(null, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cooperative = Cooperative::with(['address', 'phones'])->findOrFail($id);

        return response()->json($cooperative);
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
        $cooperative = Cooperative::findOrFail($id);

        Validator::make($request->all(), [
            'name' => ['bail', 'max:100', Rule::unique('cooperatives')->ignore($cooperative->id)],
            'dap_path' => [Rule::unique('cooperatives')->ignore($cooperative->id), 'max:100'],
            'phones.*.number' => ['distinct', Rule::unique('phones')->whereNotIn('id', $cooperative->phones->modelKeys()), 'max:15'],
            'city' => 'max:100',
            'street' => 'max:100',
            'neighborhood' => 'max:100',
            'number' => 'max:10',
        ])->validate();

        $cooperative->fill($request->all());

        if (!empty($phones = $request->input('phones'))) {
            $cooperative->phones()->delete();
            $cooperative->phones()->createMany($phones);
        }

        $address = Address::findOrFail($cooperative->address_id);
        $address->fill($request->all());
        $address->update();

        return response()->json(null, 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cooperative = Cooperative::findOrFail($id);

        $cooperative->phones()->delete();
        $cooperative->delete();
        $cooperative->address()->delete();

        return response()->json(null, 204);
    }
}
