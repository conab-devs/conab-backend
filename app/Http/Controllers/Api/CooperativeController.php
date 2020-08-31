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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'phone' => 'required|unique:phones,number|max:15',
            'city' => 'required|max:100',
            'street' => 'required|max:100',
            'neighborhood' => 'required|max:100',
            'number' => 'required|max:10',
        ]);

        DB::beginTransaction();

        $address = Address::create($request->only(['city', 'street', 'neighborhood', 'number']));
        $phone = Phone::create(['number' => $request['phone']]);

        $cooperative = new Cooperative();
        $cooperative->fill($request->only($cooperative->getFillable()));
        $cooperative->address_id = $address->id;
        $cooperative->save();
        $cooperative->phones()->attach($phone->id);

        if (!$address || !$phone || !$cooperative) {
            DB::rollBack();
            return response()->json([
                    'message' => 'Failure create cooperative.'
            ], 400);
        }

        DB::commit();
        return response()->json(null, 204);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cooperative = Cooperative::with(['address', 'phones'])
            ->where('id', $id)
            ->first();

        if (!$cooperative) {
            return response()->json([
                'message' => 'Cooperative not found.'
            ], 404);
        }

        return response()->json($cooperative);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        $cooperative = Cooperative::findOrFail($id);

        Validator::make($request->all(), [
            'name' => ['bail', 'max:100', Rule::unique('cooperatives')->ignore($cooperative->id)],
            'dap_path' => [Rule::unique('cooperatives')->ignore($cooperative->id), 'max:100'],
            'phones.*.number' => [Rule::unique('phones')->whereNotIn('id', $cooperative->phones->modelKeys()), 'max:15'],
            'city' => 'max:100',
            'street' => 'max:100',
            'neighborhood' => 'max:100',
            'number' => 'max:10',
        ])->validate();

        $cooperative->fill($request->only($cooperative->getFillable()));

        if (!empty($phones = $request->input('phones'))) {
            $cooperative->phones()->delete();
            $cooperative->phones()->createMany($phones);
        }

        if (!empty($addressData = $request->only($cooperative->address->getFillable())))  {
            $address = Address::findOrFail($cooperative->address_id);
            $address->fill($addressData);
            $address->update();
        }

        if (!$cooperative->update()) {
            return response()->json([
                'message' => 'Failure to update cooperative.'
            ], 400);
        }

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
        $cooperative = Cooperative::with(['address', 'phones'])
            ->where('id', $id)
            ->first();

        if (!$cooperative) {
            return response()->json([
                'message' => 'Cooperative not found.'
            ], 404);
        }

        DB::beginTransaction();
        $phones = $cooperative->phones()->delete();
        $coop = $cooperative->delete();
        $address = $cooperative->address()->delete();

        if (!$coop || !$address || !$phones) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failure to delete cooperative.'
            ], 400);
        }

        DB::commit();
        return response()->json(null, 204);
    }
}
