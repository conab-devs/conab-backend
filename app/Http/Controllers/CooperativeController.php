<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Address;
use App\Cooperative;
use App\Components\Traits\UploadFirebase;

class CooperativeController extends Controller
{
    use UploadFirebase;

    public function __construct()
    {
        $this->middleware('only-admin-conab');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
            return response()->json('Falha ao enviar o DAP', 400);
        }

        $address = Address::create($request->only(['city', 'street', 'neighborhood', 'number']));

        $cooperative->fill($request->except(['dap_path']));
        $cooperative->address_id = $address->id;
        $cooperative->save();
        $cooperative->phones()->createMany($request->input('phones'));

        return response()->json($cooperative->load(['address', 'phones']), 201);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $cooperative = Cooperative::with(['address', 'phones'])->findOrFail($id);

        return response()->json($cooperative);
    }


    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
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

        return response()->json(null, 204);
    }


    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDap(Request $request, int $id)
    {
        $cooperative = Cooperative::findOrFail($id);

        $request->validate([
            'dap_path' => 'required|mimetypes:application/pdf',
        ]);

        $cooperative->dap_path = !$request->hasFile('dap_path')
            ?: $this->uploadDap($request->file('dap_path'));

        if (!$cooperative->dap_path) {
            return response()->json('Falha ao enviar o DAP', 400);
        }

        $cooperative->update();

        return response()->json(null, 204);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $cooperative = Cooperative::findOrFail($id);

        $cooperative->phones()->delete();
        $cooperative->delete();
        $cooperative->address()->delete();

        return response()->json(null, 204);
    }

    /**
     * @param UploadedFile $dap
     * @return string|null
     */
    private function uploadDap(UploadedFile $dap): ?string
    {
        if (!$dap->isValid()) {
            return null;
        }

        return App::environment('production')
            ? $this->uploadFileOnFirebase($dap)
            : $dap->store('uploads');
    }
}
