<?php

namespace App\Services\Api;

use App\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Repositories\Contract\TranslationExportServiceInterface;
use App\Repositories\Contract\TranslationExportRepositoryInterface;

class TranslationExportService implements TranslationExportServiceInterface
{
    protected TranslationExportRepositoryInterface $repository;

    public function __construct(TranslationExportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    // Export: latest payload per key for a locale, with optional tag filtering
    public function exportTranslations(Request $request)
    {
        $localeCode = $request->query('locale');
        $tagFilters = array_filter(explode(',', $request->query('tags', '')));

        $locale = Locale::whereCode($localeCode)->firstOrFail();
        $translations = $this->repository->getTranslationsForLocaleByTags($locale->id, $tagFilters);

        // Build the streaming response content as before
        $callback = function() use ($translations) {
            yield '{"translations":{';
            $first = true;
            foreach ($translations as $t) {
                $entry = json_encode($t->key) . ':' . json_encode($t->content ?? '');
                if (!$first) {
                    echo ',';
                }
                echo $entry;
                $first = false;
            }
            yield '}}';
        };

        // Return a streamable response (you might adapt to Laravel's Response::stream)
        return response()->stream($callback, 200, ['Content-Type' => 'application/json']);
    }

    // Export all locales payload
    public function exportAllLocales()
    {
        $callback = function() {
            yield '{';
            $firstLocale = true;
            foreach ($this->repository->getAllLocales() as $locale) {
                if (!$firstLocale) yield ',';
                $firstLocale = false;
                yield json_encode($locale->code) . ':' . json_encode(
                    // map of translations per key -> content for this locale
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
