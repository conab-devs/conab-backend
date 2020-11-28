<?php

namespace App\Http\Controllers;

use App\Http\Requests\Address\StoreRequest;
use App\Http\Requests\Address\UpdateRequest;

class AddressController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return response()->json($user->addresses()->get());
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $user = auth()->user();

        $addresses = $user->addresses()
            ->createMany($validated['addresses']);

        return response()->json($addresses, 201);
    }

    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();

        $user = auth()->user();
        $user->addresses()->delete();

        $addresses = $user->addresses()
            ->createMany([$validated['addresses']]);

        return response()->json($addresses);
    }
}
