<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_api_users_me_route(): void
    {
        $this->withoutExceptionHandling();

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson(route('api.users.me'));

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->toISOString(),
                'updated_at' => $user->updated_at->toISOString(),
            ],
        ]);
    }
}
