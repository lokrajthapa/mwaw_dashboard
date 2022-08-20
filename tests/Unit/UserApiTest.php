<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_if_api_crud_are_working()
    {
        $user = User::first();
        $this->assertNotNull($user);

        $this->actingAs($user)
            ->get(route('users.index'))->assertJsonStructure(
                ['data', 'links', 'meta']
            );


        $this->actingAs($user)
            ->get(route('users.show', ['user' => 1]))->assertJson(['data' => ['id' => 1]]);

        $data = User::factory()->make()->attributesToArray();
        $data['password'] = 'password';


        $response = $this->actingAs($user)
            ->post(route('users.store'), $data);

        unset($data['password']);
        unset($data['sf_id']);
        unset($data['email_verified_at']);

        $response->assertJson(['data' => $data]);

        unset($data['display_role']);
        $this->assertDatabaseHas('users', $data);


        $id = $response->json()['data']['id'];

        $data['name'] = 'User2modified';
        $data['email'] = 'user2emailmodified@gmail.com';
        $this->actingAs($user)
            ->put(route('users.update', ['user' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('users', $data);

        $this->actingAs($user)
            ->delete(route('users.destroy', ['user' => $id]));

        $this->assertDatabaseMissing('users', $data);

    }
}
