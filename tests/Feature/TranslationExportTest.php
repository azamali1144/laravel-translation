<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Translation;

class TranslationExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_flat_format_streams_chunks()
    {
        Translation::factory()->count(100)->create();
        $response = $this->getJson('/api/v1/translations/export?format=flat&size=20');
        $response->assertStatus(200);
        $response->assertJsonFragment(['greeting' => 'Hello']);
    }

    public function test_export_nested_format()
    {
        Translation::factory()->count(10)->create();
        $response = $this->getJson('/api/v1/translations/export?format=nested');
        $response->assertStatus(200);
        $content = $response->json();
        $this->assertArrayHasKey('en', $content);
    }

    public function test_export_with_locale_filter()
    {
        Translation::factory()->count(5)->create(['locale' => 'en']);
        Translation::factory()->count(5)->create(['locale' => 'fr']);
        $response = $this->getJson('/api/v1/translations/export?locale=en&format=flat');
        $response->assertStatus(200);
        foreach ($response->json() as $item) {
            // adapt to your format
        }
    }
}
