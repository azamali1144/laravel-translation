<?php
namespace App\Repositories\Contract;

use App\Models\Locale;

interface LocaleServiceInterface
{
    public function getAllLocales();
    public function createLocale(string $code, string $name): Locale;
    public function localeExists(string $code): bool;
}
