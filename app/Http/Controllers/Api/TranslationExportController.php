<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contract\TranslationExportServiceInterface;
use Illuminate\Http\Request;

class TranslationExportController extends Controller
{
    protected TranslationExportServiceInterface $service;

    public function __construct(TranslationExportServiceInterface $service)
    {
        $this->service = $service;
    }

    // Simple export: latest payload per key for a locale, with optional tag filtering
    public function export(Request $request)
    {
        return $this->service->exportTranslations($request);
    }

    public function exportAll()
    {
        return $this->service->exportAllLocales();
    }
}
