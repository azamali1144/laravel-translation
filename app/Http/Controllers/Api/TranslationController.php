<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TranslationController extends Controller
{
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

        $locale = Locale::whereCode($validated['locale_code'])->firstOrFail();

        // Transactional upsert with tag sync
        DB::transaction(function() use ($locale, $validated) {
            $trans = Translation::firstOrCreate(
                ['locale_id' => $locale->id, 'key' => $validated['key']],
                ['content' => $validated['content'] ?? null]
            );

            if (isset($validated['content'])) {
                $trans->content = $validated['content'];
                $trans->save();
            }

            if (!empty($validated['tags'])) {
                $tagIds = [];
                foreach ($validated['tags'] as $name) {
                    $tag = Tag::firstOrCreate(['name' => strtolower(trim($name))]);
                    $tagIds[] = $tag->id;
                }
                $trans->tags()->sync($tagIds);
            }
        });

        return response()->json(['data' => Translation::with(['tags', 'locale'])->findOrFail(
            Translation::whereLocaleId($locale->id)->where('key', $validated['key'])->value('id')
        )]);
    }

    public function index(Request $request)
    {
        $query = Translation::with(['tags', 'locale'])->query();

        if ($localeCode = $request->query('locale')) {
            $locale = Locale::whereCode($localeCode)->first();
            if ($locale) {
                $query->where('locale_id', $locale->id);
            }
        }

        if ($request->filled('key')) {
            $query->where('key', 'like', '%' . $request->query('key') . '%');
        }

        if ($request->filled('content')) {
            $query->where('content', 'like', '%' . $request->query('content') . '%');
        }

        if ($request->filled('tags')) {
            $tags = explode(',', $request->query('tags'));
            $query->whereHas('tags', function($q) use ($tags){
                $q->whereIn('name', $tags);
            });
        }

        return response()->json(['data' => $query->paginate($request->get('per_page', 25))]);
    }

    public function show($id)
    {
        $trans = Translation::with(['tags', 'locale'])->findOrFail($id);
        return response()->json(['data' => $trans]);
    }

    public function update(Request $request, $id)
    {
        $trans = Translation::findOrFail($id);
        $validated = $request->validate([
            'content' => 'nullable|string',
            'tags' => 'array',
            'tags.*' => 'string',
        ]);

        if (isset($validated['content'])) {
            $trans->content = $validated['content'];
        }

        if (isset($validated['tags'])) {
            $tagIds = Tag::firstOrCreateTags($validated['tags']);
            $trans->tags()->sync($tagIds);
        }

        $trans->save();

        return response()->json(['data' => $trans->load('tags', 'locale')]);
    }

    public function destroy($id)
    {
        $trans = Translation::findOrFail($id);
        $trans->delete();
        return response()->json(['data' => null], 204);
    }

    // Optional: search endpoint
    public function search(Request $request)
    {
        $query = Translation::with(['tags', 'locale'])->query();

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function($qq) use ($q){
                $qq->where('key', 'like', "%$q%")
                   ->orWhere('content', 'like', "%$q%");
            });
        }

        if ($request->filled('tags')) {
            $tags = explode(',', $request->query('tags'));
            $query->whereHas('tags', function($qq) use ($tags){
                $qq->whereIn('name', $tags);
            });
        }

        return response()->json(['data' => $query->paginate($request->get('per_page', 25))]);
    }
}
