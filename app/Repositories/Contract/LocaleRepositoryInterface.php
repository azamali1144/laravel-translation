<?php
namespace App\Repositories\Contract;

use App\Models\Locale;

interface LocaleRepositoryInterface
{
    public function all();
    public function create(array $attributes): Locale;
    public function existsWithCode(string $code): bool;
}
