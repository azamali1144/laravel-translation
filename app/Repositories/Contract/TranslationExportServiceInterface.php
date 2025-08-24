<?php
namespace App\Repositories\Contract;

use Illuminate\Http\Request;

interface TranslationExportServiceInterface
{
    public function exportTranslations(Request $request);
    public function exportAllLocales();
}
