<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contract\TranslationServiceInterface;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    protected TranslationServiceInterface $service;

    public function __construct(TranslationServiceInterface $service)
    {
        $this->service = $service;
    }

    // Create or upsert by locale_code + key
    public function store(Request $request)
    {
        $validated = $request->validate([
            'locale_code' => 'required|string|size:2',
            'key' => 'required|string',
            'content' => 'nullable|string',
            'tags' => 'array',
            'tags.*' => 'string',
        ]);

        $trans = $this->service->store(
            $validated['locale_code'],
            $validated['key'],
            $validated['content'] ?? null,
            $validated['tags'] ?? []
        );

        return response()->json(['data' => $trans]);
    }

    public function index(Request $request)
    {
        $filters = [
            'locale' => $request->query('locale'),
            'key' => $request->query('key'),
            'content' => $request->query('content'),
            'tags' => $request->query('tags'),
        ];
        $perPage = (int) $request->get('per_page', 25);

        $translations = $this->service->list($filters, $perPage);

        return response()->json(['data' => $translations]);
    }

    public function show($id)
    {
        $trans = $this->service->show((int) $id);
        return response()->json(['data' => $trans]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'nullable|string',
            'tags' => 'array',
            'tags.*' => 'string',
        ]);

        $trans = $this->service->update((int) $id, $validated['content'] ?? null, $validated['tags'] ?? null);

        return response()->json(['data' => $trans]);
    }

    public function destroy($id)
    {
        $this->service->destroy((int) $id);
        return response()->json(['data' => null], 204);
    }

    // Optional: search endpoint
    public function search(Request $request)
    {
        $filters = [
            'q' => $request->query('q'),
            'tags' => $request->query('tags'),
        ];
        $perPage = (int) $request->get('per_page', 25);

        $results = $this->service->search($filters, $perPage);

        return response()->json(['data' => $results]);
    }
}
