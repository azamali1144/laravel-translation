<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contract\LocaleServiceInterface;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    protected LocaleServiceInterface $service;

    public function __construct(LocaleServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $locales = $this->service->getAllLocales();
        return response()->json(['data' => $locales]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:2|unique:locales,code',
            'name' => 'required|string',
        ]);

        $locale = $this->service->createLocale($validated['code'], $validated['name']);

        return response()->json(['data' => $locale], 201);
    }
}
