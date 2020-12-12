<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use App\Address;
use App\Cooperative;
use App\Components\Traits\UploadFirebase;
use App\Http\Requests\Cooperative\StoreRequest;
use App\Http\Requests\Cooperative\UpdateRequest;

/**
 * @OA\Schema(
 *     schema="CooperativeRequest",
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nome da cooperativa"
 *     ),
 *     @OA\Property(
 *         property="dap_path",
 *         type="string",
 *         format="base64",
 *         description="Arquivo do DAP no format PDF"
 *     ),
 *     @OA\Property(
 *         property="city",
 *         type="string",
 *         description="Cidade da cooperativa"
 *     ),
 *     @OA\Property(
 *         property="street",
 *         type="string",
 *         description="Rua da cooperativa"
 *     ),
 *     @OA\Property(
 *         property="neighborhood",
 *         type="string",
 *         description="Bairro da cooperativa"
 *     ),
 *     @OA\Property(
 *         property="number",
 *         type="string",
 *         description="Número do endereço da cooperativa"
 *     ),
 * )
 */
class CooperativeController extends Controller
{
    use UploadFirebase;

    public function __construct()
    {
        $this->middleware('only-admin-conab');
    }

    /**
     * @OA\Get(
     *     path="/cooperatives",
     *     operationId="index",
     *     summary="Retorna uma lista de cooperativas",
     *     tags={"Cooperativas"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="CooperativeResponse",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/Cooperative"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="phones",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Phone")
     *                         )
     *                     ),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="addresses",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Address")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server Error")
     * )
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
     * @OA\Get(
     *     path="/cooperatives/{cooperativeId}",
     *     operationId="show",
     *     summary="Retorna uma cooperativa especifica",
     *     tags={"Cooperativas"},
     *
     *     @OA\Parameter(
     *         name="cooperativeId",
     *         description="Id da cooperativa",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="CooperativeResponse",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/Cooperative"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="phones",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Phone")
     *                         )
     *                     ),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="addresses",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Address")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function show(int $id)
    {
        $cooperative = Cooperative::with(['address', 'phones'])->findOrFail($id);

        return response()->json($cooperative);
    }

    /**
     * @OA\Post(
     *     path="/cooperatives",
     *     operationId="store",
     *     summary="Registra uma nova cooperativa",
     *     tags={"Cooperativas"},
     *
     *     @OA\RequestBody(
     *         request="Cooperative",
     *         description="Objeto da cooperativa",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 schema="CooperativeRequest",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/CooperativeRequest"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="phones",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Phone")
     *                         )
     *                     ),
     *                     @OA\Schema(ref="#/components/schemas/Address")
     *                 }
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="CooperativeResponse",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/Cooperative"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="phones",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Phone")
     *                         )
     *                     ),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="address",
     *                             type="obejct",
     *                             @OA\Schema(ref="#/components/schemas/Address")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=400, description="Bad Eequest"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/cooperatives/{cooperativeId}",
     *     operationId="update",
     *     summary="Atualiza uma cooperativa",
     *     tags={"Cooperativas"},
     *
     *     @OA\Parameter(
     *         name="cooperativeId",
     *         description="Id da cooperativa",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         request="Cooperative",
     *         description="Objeto da cooperativa",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="CooperativeRequest",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/CooperativeRequest"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="phones",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Phone")
     *                         )
     *                     ),
     *                     @OA\Schema(ref="#/components/schemas/Address")
     *                 }
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=204, description="Not Content"),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/cooperatives/{cooperativeId}",
     *     operationId="updateDap",
     *     summary="Atualiza o DAP da cooperativa",
     *     tags={"Cooperativas"},
     *
     *     @OA\Parameter(
     *         name="cooperativeId",
     *         description="Id da cooperativa",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         request="DAP",
     *         description="Arquivo DAP da cooperativa",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="dap_path",
     *                     type="string",
     *                     format="base64",
     *                     description="Arquivo do DAP no format PDF"
     *                 ),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=204, description="Not Content"),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/cooperatives/{cooperativeId}",
     *     operationId="destroy",
     *     summary="Exclui uma cooperativa",
     *     tags={"Cooperativas"},
     *
     *     @OA\Parameter(
     *         name="cooperativeId",
     *         description="Id da cooperativa",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response=204, description="Not Content"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
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
