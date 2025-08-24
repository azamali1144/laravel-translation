<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Locale::all()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:2|unique:locales,code',
            'name' => 'required|string',
        ]);

        $locale = Locale::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
        ]);

        return response()->json(['data' => $locale], 201);
    }
}
