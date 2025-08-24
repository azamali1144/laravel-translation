<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Translation;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_translation_model()
    {
        $t = Translation::factory()->create([
            'locale' => 'en',
            'key' => 'greeting.hello',
            'content' => json_encode(['text' => 'Hello']),
        ]);

        $this->assertDatabaseHas('translations', ['id' => $t->id]);
        $this->assertEquals('en', $t->locale);
        $this->assertJson($t->content);
    }

    public function test_translation_tags_storage()
    {
        $t = Translation::factory()->create([
            'tags' => json_encode(['mobile','web']),
        ]);

        $this->assertJson($t->tags);
        $this->assertArrayHasKey('mobile', json_decode($t->tags, true) ?? []);
    }
}
