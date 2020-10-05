<?php

namespace App\Http\Controllers;

use App\Address;
use App\Cooperative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CooperativeController extends Controller
{
    public function __construct()
    {
        $this->middleware('only-admin-conab');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cooperatives = Cooperative::with(['address', 'phones'])
            ->when($request->name, function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })->paginate(10);

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
            'dap_path' => 'required|mimetypes:application/pdf',
            'phones' => 'required|array',
            'phones.*.number' => 'required|distinct|regex:/(\(\d{2}\)\ \d{4,5}\-\d{4})/|unique:phones,number|max:15',
            'city' => 'required|max:100',
            'street' => 'required|max:100',
            'neighborhood' => 'required|max:100',
            'number' => 'required|max:10',
        ]);

        $cooperative = new Cooperative();

        if ($request->hasFile('dap_path') && ($request->file('dap_path')->isValid())) {
            $path = $request->file('dap_path')->store('uploads');
            $cooperative->dap_path = $path;
        } else {
            return response()->json('Failed to send DAP.', 400);
        }

        $address = Address::create($request->only(['city', 'street', 'neighborhood', 'number']));

        $cooperative->fill($request->except(['dap_path']));
        $cooperative->address_id = $address->id;
        $cooperative->save();
        $cooperative->phones()->createMany($request->input('phones'));

        return response()->json($cooperative->load(['address', 'phones']), 201);
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
            'name' => ['bail', Rule::unique('cooperatives', 'name')->ignore($cooperative->id), 'max:100'],
            'phones' => 'array',
            'phones.*.number' => [
                'distinct',
                Rule::unique('phones')->whereNotIn('id', $cooperative->phones->modelKeys()),
                'regex:/(\(\d{2}\)\ \d{4,5}\-\d{4})/',
                'max:15'
            ],
            'city' => 'max:100',
            'street' => 'max:100',
            'neighborhood' => 'max:100',
            'number' => 'max:10',
        ])->validate();

        $cooperative->name = $request->input('name');
        $cooperative->update();

        if (!empty($phones = $request->input('phones'))) {
            $cooperative->phones()->delete();
            $cooperative->phones()->createMany($phones);
        }

        $address = Address::findOrFail($cooperative->address_id);
        $address->fill($request->all());
        $address->update();

        return response()->json(null, 204);
    }

    public function updateDap(Request $request, $id)
    {
        $cooperative = Cooperative::findOrFail($id);

        $request->validate([
            'dap_path' => 'required|mimetypes:application/pdf',
        ]);

        if ($request->hasFile('dap_path') && ($request->file('dap_path')->isValid())) {
            $path = $request->file('dap_path')->store('uploads');
            Storage::delete($cooperative->dap_path);
            $cooperative->dap_path = $path;
        } else {
            return response()->json('Failed to send DAP.', 400);
        }

        $cooperative->update();

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
