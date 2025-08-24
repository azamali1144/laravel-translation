<?php

namespace App\Repositories\Eloquent;

use App\Models\Locale;
use App\Repositories\Contract\LocaleRepositoryInterface;

class LocaleRepository implements LocaleRepositoryInterface
{
    public function all()
    {
        return Locale::all();
    }

    public function create(array $attributes): Locale
    {
        return Locale::create($attributes);
    }

    public function existsWithCode(string $code): bool
    {
        return Locale::whereCode($code)->exists();
    }
}
