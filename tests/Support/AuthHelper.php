<?php
namespace Tests\Support;

use App\Models\User;

trait AuthHelper {
    protected function actingAsApiUser()
    {
        $user = User::factory()->create();
        return $user->createToken('test-token')->plainTextToken;
    }

    protected function withApiHeaders($user = null)
    {
        $headers = [
            'Accept' => 'application/json',
        ];
        if ($user) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }
        return $headers;
    }
}
