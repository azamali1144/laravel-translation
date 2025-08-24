<?php
namespace App\Repositories\Contract;

use App\Models\Translation;

interface TranslationServiceInterface
{
    public function store(string $localeCode, string $key, ?string $content, array $tags = []): Translation;
    public function list(array $filters, int $perPage = 25);
    public function show(int $id): Translation;
    public function update(int $id, ?string $content, ?array $tags): Translation;
    public function destroy(int $id): void;
    public function search(array $filters, int $perPage = 25);
}
