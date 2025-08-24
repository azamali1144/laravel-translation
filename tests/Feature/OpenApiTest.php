<?php

namespace Tests\Feature;

use Tests\TestCase;

class OpenApiTest extends TestCase
{
    public function test_openapi_yaml_is_accessible()
    {
        $response = $this->get('/openapi.yaml');
        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'application/x-yaml');
    }

    public function test_docs_route_renders_redoc()
    {
        $response = $this->get('/docs');
        $response->assertStatus(200);
    }
}
