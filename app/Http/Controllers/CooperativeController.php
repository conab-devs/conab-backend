<?php

namespace App\Http\Controllers;

use App\Address;
use App\Cooperative;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Components\Traits\UploadFirebase;

class CooperativeController extends Controller
{
    use UploadFirebase;

    public function __construct()
    {
        $this->middleware('only-admin-conab');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cooperatives = Cooperative::with(['address', 'phones'])
            ->when($request->name, function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })->paginate(10);

        return response($cooperatives);
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
            'phones.*.number' =>
                'required|distinct|regex:/(\(\d{2}\)\ \d{4,5}\-\d{4})/|unique:phones,number|max:15',
            'city' => 'required|max:100',
            'street' => 'required|max:100',
            'neighborhood' => 'required|max:100',
            'number' => 'required|max:10',
        ]);

        $cooperative = new Cooperative();

        $cooperative->dap_path = !$request->hasFile('dap_path')
            ?: $this->uploadDap($request->file('dap_path'));

        if (!$cooperative->dap_path) {
            return response('Falha ao enviar o DAP', 400);
        }

        $address = Address::create($request->only(['city', 'street', 'neighborhood', 'number']));

        $cooperative->fill($request->except(['dap_path']));
        $cooperative->address_id = $address->id;
        $cooperative->save();
        $cooperative->phones()->createMany($request->input('phones'));

        return response($cooperative->load(['address', 'phones']), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $cooperative = Cooperative::with(['address', 'phones'])->findOrFail($id);

        return response($cooperative);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $cooperative = Cooperative::findOrFail($id);

        Validator::make($request->all(), [
            'name' => [
                'bail',
                Rule::unique('cooperatives', 'name')->ignore($cooperative->id),
                'max:100'
            ],
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

        return response(null, 204);
    }

    public function updateDap(Request $request, int $id)
    {
        $cooperative = Cooperative::findOrFail($id);

        $request->validate([
            'dap_path' => 'required|mimetypes:application/pdf',
        ]);

        $cooperative->dap_path = !$request->hasFile('dap_path')
            ?: $this->uploadDap($request->file('dap_path'));

        if (!$cooperative->dap_path) {
            return response('Falha ao enviar o DAP', 400);
        }

        $cooperative->update();

        return response(null, 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $cooperative = Cooperative::findOrFail($id);

        $cooperative->phones()->delete();
        $cooperative->delete();
        $cooperative->address()->delete();

        return response(null, 204);
    }

    private function uploadDap(UploadedFile $dap): ?string
    {
        if (!$dap->isValid()) return null;

        return App::environment('production')
            ? $this->uploadFileOnFirebase($dap)
            : $dap->store('uploads');
    }
}
