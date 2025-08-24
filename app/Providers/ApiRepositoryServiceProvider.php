<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

// Bindings for Repositories and Services

use App\Services\Api\LocaleService;
use App\Services\Api\TranslationService;
use App\Services\Api\TranslationExportService;

use App\Repositories\Eloquent\LocaleRepository;
use App\Repositories\Eloquent\TranslationRepository;
use App\Repositories\Eloquent\TranslationExportRepository;

use App\Repositories\Contract\LocaleServiceInterface;
use App\Repositories\Contract\LocaleRepositoryInterface;
use App\Repositories\Contract\TranslationServiceInterface;
use App\Repositories\Contract\TranslationRepositoryInterface;
use App\Repositories\Contract\TranslationExportServiceInterface;
use App\Repositories\Contract\TranslationExportRepositoryInterface;

class ApiRepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(LocaleRepositoryInterface::class, LocaleRepository::class);
        $this->app->bind(LocaleServiceInterface::class, LocaleService::class);

        $this->app->bind(TranslationRepositoryInterface::class, TranslationRepository::class);
        $this->app->bind(TranslationServiceInterface::class, TranslationService::class);

        $this->app->bind(TranslationExportRepositoryInterface::class, TranslationExportRepository::class);
        $this->app->bind(TranslationExportServiceInterface::class, TranslationExportService::class);
    }

    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
