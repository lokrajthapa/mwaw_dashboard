<?php

namespace Tests\Unit;

use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusApiTest extends TestCase
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

        $data = Status::factory()->make()->attributesToArray();
        unset($data['sf_id']);
        $response = $this->actingAs($user)
            ->post(route('statuses.store'), $data);

        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('statuses', $data);

        $id = $response->json()['data']['id'];

        $data['name'] = 'Modified';
        $this->actingAs($user)
            ->put(route('statuses.update', ['status' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('statuses', $data);

        $this->actingAs($user)
            ->get(route('statuses.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('statuses.show', ['status' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('statuses.show', ['status' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->delete(route('statuses.destroy', ['status' => $id]));

        $this->assertDatabaseMissing('statuses', $data);

    }
}
