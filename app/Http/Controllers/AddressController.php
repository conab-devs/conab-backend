<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return response()->json($user->addresses()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'addresses' => 'required|array',
            'addresses.*.street' => 'required|string',
            'addresses.*.neighborhood' => 'required|string',
            'addresses.*.city' => 'required|string',
            'addresses.*.number' => 'required|string'
        ]);

        $user = auth()->user();

        $addresses = $user->addresses()
            ->createMany($validated['addresses']);

        return response()->json($addresses, 201);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'addresses' => 'array',
            'addresses.*.street' => 'string',
            'addresses.*.neighborhood' => 'string',
            'addresses.*.city' => 'string',
            'addresses.*.number' => 'string',
        ]);

        $user = auth()->user();
        $user->addresses()->delete();

        $addresses = $user->addresses()
            ->createMany([$validated['addresses']]);

        return response()->json($addresses);
    }
}
