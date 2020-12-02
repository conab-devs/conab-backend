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
use App\Http\Requests\Cooperative\StoreRequest;
use App\Http\Requests\Cooperative\UpdateRequest;

class CooperativeController extends Controller
{
    use UploadFirebase;

    public function __construct()
    {
        $this->middleware('only-admin-conab');
    }

    public function index(Request $request)
    {
        $cooperatives = Cooperative::with(['address', 'phones'])
            ->when($request->name, function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })->paginate(10);

        return response()->json($cooperatives);
    }

    public function show(int $id)
    {
        $cooperative = Cooperative::with(['address', 'phones'])->findOrFail($id);

        return response()->json($cooperative);
    }

    public function store(StoreRequest $request)
    {
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
        $cooperative->load(['address', 'phones']);

        return response()->json($cooperative, 201);
    }

    public function update(UpdateRequest $request, Cooperative $cooperative)
    {
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

    public function updateDap(Request $request, Cooperative $cooperative)
    {
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

    public function destroy(Cooperative $cooperative)
    {
        $cooperative->phones()->delete();
        $cooperative->delete();
        $cooperative->address()->delete();

        return response()->json(null, 204);
    }

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
