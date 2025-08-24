<?php

namespace App\Services\Api;

use App\Models\Locale;
use App\Repositories\Contract\LocaleServiceInterface;
use App\Repositories\Contract\LocaleRepositoryInterface;

class LocaleService implements LocaleServiceInterface
{
    protected LocaleRepositoryInterface $repository;

    public function __construct(LocaleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllLocales()
    {
        return $this->repository->all();
    }

    public function createLocale(string $code, string $name): Locale
    {
        // Validation is still done at the controller level in the original flow.
        return $this->repository->create([
            'code' => $code,
            'name' => $name,
        ]);
    }

    public function localeExists(string $code): bool
    {
        return $this->repository->existsWithCode($code);
    }
}
