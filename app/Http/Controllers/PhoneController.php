<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhoneController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return response()->json($user->phones()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phones' => 'required|array',
            'phones.*.number' => 'required|string|regex:/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/|distinct|unique:phones,number',
        ]);

        $user = auth()->user();

        $phones = $user->phones()->createMany($validated['phones']);

        return response()->json($phones, 201);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'phones' => 'array',
            'phones.*.number' => 'string|regex:/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/|distinct|unique:phones,number',
        ]);

        $user = auth()->user();
        $user->phones()->delete();

        $phones = $user->phones()->createMany($validated['phones']);

        return response()->json($phones);
    }
}
