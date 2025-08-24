<?php
namespace App\Repositories\Contract;

use App\Models\Translation;

interface TranslationRepositoryInterface
{
    public function findById(int $id): Translation;
    public function findOrFail(int $id): Translation;
    public function whereLocaleAndKey(int $localeId, string $key): ?Translation;
    public function upsertByLocaleAndKey(int $localeId, string $key, ?string $content): Translation;
    public function updateContent(Translation $trans, ?string $content): void;
    public function syncTags(Translation $trans, array $tagNames): void;
    public function delete(int $id): void;
    public function withRelations(): array;
    public function paginate($query, int $perPage = 25);
}
