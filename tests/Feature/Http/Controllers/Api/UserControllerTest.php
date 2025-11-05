<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_api_users_index_route(): void
    {
        $this->withoutExceptionHandling();

        User::factory()->count(20)->create();

        $response = $this->getJson(route('api.users.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ]);
    }

    public function test_api_users_store_route(): void
    {
        $this->withoutExceptionHandling();

        $payload = [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('2547########'),
            'address' => fake()->streetAddress(),
            'pin' => fake()->numerify('####'),
        ];

        $response = $this->postJson(route('api.users.store'), $payload);

        $response->assertCreated();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'phone',
                'address',
                'pin',
            ],
        ]);

        $this->assertDatabaseHas(User::class, $payload);
    }

    public function test_api_users_show_route(): void
    {
        $this->withoutExceptionHandling();

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->getJson(route('api.users.show', $user));

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'pin' => $user->pin,
            ],
        ]);
    }

    public function test_api_users_update_route(): void
    {
        $this->withoutExceptionHandling();

        /** @var User $user */
        $user = User::factory()->create();

        $payload = [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('2547########'),
            'address' => fake()->streetAddress(),
            'pin' => fake()->numerify('####'),
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson(route('api.users.update', $user), $payload);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'phone',
                'address',
                'pin',
            ],
        ]);

        $this->assertDatabaseHas(User::class, array_merge(['id' => $user->id], $payload));
    }

    public function test_api_users_destroy_route_soft(): void
    {
        $this->withoutExceptionHandling();

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson(route('api.users.destroy', $user));

        $response->assertNoContent();

        $this->assertSoftDeleted(User::class, [
            'id' => $user->id,
        ]);
    }

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
