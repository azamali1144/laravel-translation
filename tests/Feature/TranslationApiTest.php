<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Translation;
use App\Models\User;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->authUser = User::factory()->create();
    }

    public function test_can_list_locales()
    {
        Translation::factory()->count(5)->create(['locale' => 'en']);
        $response = $this->getJson('/api/v1/locales');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_can_create_translation_requires_auth()
    {
        $payload = [
            'locale' => 'en',
            'key' => 'greeting.hello',
            'content' => ['text' => 'Hello'],
            'tags' => ['mobile']
        ];

        $response = $this->postJson('/api/v1/translations', $payload);
        $response->assertStatus(401); // or 403 depending on your auth
    }

    public function test_can_create_translation_with_auth()
    {
        $user = User::factory()->create();
        $payload = [
            'locale' => 'en',
            'key' => 'greeting.hello',
            'content' => ['text' => 'Hello'],
            'tags' => ['mobile']
        ];

        $response = $this->actingAs($user, 'api')->postJson('/api/v1/translations', $payload);
        $response->assertStatus(201);
        $this->assertDatabaseHas('translations', ['locale' => 'en', 'key' => 'greeting.hello']);
    }

    public function test_can_view_translation()
    {
        $t = Translation::factory()->create(['locale' => 'en', 'key' => 'greeting.hello']);
        $response = $this->getJson("/api/v1/translations/{$t->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['locale' => 'en', 'key' => 'greeting.hello']);
    }

    public function test_can_update_translation()
    {
        $user = User::factory()->create();
        $t = Translation::factory()->create(['locale' => 'en', 'key' => 'greeting.hello']);

        $payload = ['content' => ['text' => 'Hi']];
        $response = $this->actingAs($user, 'api')->putJson("/api/v1/translations/{$t->id}", $payload);
        $response->assertStatus(200);
        $this->assertDatabaseHas('translations', ['id' => $t->id, 'content' => json_encode(['text' => 'Hi'])]);
    }

    public function test_can_patch_translation()
    {
        $user = User::factory()->create();
        $t = Translation::factory()->create(['locale' => 'en', 'key' => 'greeting.hello', 'content' => json_encode(['text'=>'Hello'])]);

        $response = $this->actingAs($user, 'api')->patchJson("/api/v1/translations/{$t->id}", ['content' => ['text' => 'Hey']]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('translations', ['id' => $t->id, 'content' => json_encode(['text' => 'Hey'])]);
    }

    public function test_can_delete_translation()
    {
        $user = User::factory()->create();
        $t = Translation::factory()->create();
        $response = $this->actingAs($user, 'api')->deleteJson("/api/v1/translations/{$t->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('translations', ['id' => $t->id]);
    }

    public function test_search_translations_by_tag()
    {
        Translation::factory()->create(['tags' => json_encode(['mobile','web']), 'locale' => 'en', 'key' => 'app.title']);
        Translation::factory()->create(['tags' => json_encode(['desktop']), 'locale' => 'en', 'key' => 'app.subtitle']);

        $response = $this->getJson('/api/v1/translations/search?tag=mobile');
        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function test_basic_export_endpoint_returns_200()
    {
        Translation::factory()->count(5)->create();
        $response = $this->getJson('/api/v1/translations/export');
        $response->assertStatus(200);
        // Depending on export format, assert structure
    }

    public function test_export_all_endpoint_returns_200()
    {
        Translation::factory()->count(5)->create();
        $response = $this->getJson('/api/v1/translations/export-all');
        $response->assertStatus(200);
    }
}
