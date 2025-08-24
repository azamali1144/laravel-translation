<?php

namespace App\Repositories\Eloquent;

use App\Models\Translation;
use App\Models\Locale;
use App\Repositories\Contract\TranslationExportRepositoryInterface;

class TranslationExportRepository implements TranslationExportRepositoryInterface
{
    public function getTranslationsForLocaleByTags(int $localeId, array $tagNames): Translation
    {
        $query = Translation::where('locale_id', $localeId)
            ->with('tags')
            ->orderBy('key');

        if (!empty($tagNames)) {
            $query->whereHas('tags', function ($q) use ($tagNames) {
                $q->whereIn('name', $tagNames);
            });
        }

        return $query->get();
    }

    public function getAllLocales(): Locale
    {
        return Locale::all();
    }
}
