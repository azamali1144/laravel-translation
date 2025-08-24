<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use App\Models\Locale;
use Illuminate\Http\Request;

class TranslationExportController extends Controller
{
    // Simple export: latest payload per key for a locale, with optional tag filtering
    public function export(Request $request)
    {
        $localeCode = $request->query('locale');
        $tagFilters = array_filter(explode(',', $request->query('tags', '')));

        $locale = Locale::whereCode($localeCode)->firstOrFail();

        $query = Translation::where('locale_id', $locale->id)
            ->with('tags')
            ->orderBy('key');

        if (!empty($tagFilters)) {
            $query->whereHas('tags', function($q) use ($tagFilters){
                $q->whereIn('name', $tagFilters);
            });
        }

        $callback = function() use ($query) {
            yield '{"translations":{';
            $first = true;
            $query->chunk(1000, function ($translations) use (&$first) {
                foreach ($translations as $t) {
                    $entry = json_encode($t->key) . ':' . json_encode($t->content ?? '');
                    if (!$first) {
                        echo ',';
                    }
                    echo $entry;
                    $first = false;
                }
            });
            yield '}}';
        };

        return response()->stream($callback, 200, ['Content-Type' => 'application/json']);
    }

    public function exportAll()
    {
        $callback = function() {
            yield '{';
            $firstLocale = true;
            foreach (Locale::all() as $locale) {
                if (!$firstLocale) yield ',';
                $firstLocale = false;
                yield json_encode($locale->code) . ':' . json_encode(
                    Translation::where('locale_id', $locale->id)
                        ->pluck('content', 'key')
                        ->toArray()
                );
            }
            yield '}';
        };

        return response()->stream($callback, 200, ['Content-Type' => 'application/json']);
    }
}
