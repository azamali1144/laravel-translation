<?php

namespace Tests\Performance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Translation;

class ExportPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_performance_under_threshold()
    {
        Translation::factory()->count(1000)->create();
        $start = microtime(true);
        $response = $this->getJson('/api/v1/translations/export?format=flat');
        $duration = (microtime(true) - $start) * 1000;
        $response->assertStatus(200);
        $this->assertLessThan(500, $duration, "Export took {$duration} ms");
    }
}
