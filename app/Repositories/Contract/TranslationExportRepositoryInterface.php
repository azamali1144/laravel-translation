<?php
namespace App\Repositories\Contract;

use App\Models\Translation;

interface TranslationExportRepositoryInterface
{
    public function getTranslationsForLocaleByTags(int $localeId, array $tagNames): Translation;
    public function getAllTranslationsForLocale(int $localeId): Translation;
    public function getAllLocales(): Translation;
    public function getTranslationContentMap(int $localeId, string $localeCode): array;
}
