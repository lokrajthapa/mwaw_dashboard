<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleApiTest extends TestCase
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

        $data = Vehicle::factory()->make()->attributesToArray();

        $response = $this->actingAs($user)
            ->post(route('vehicles.store'), $data);

        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('vehicles', $data);

        $id = $response->json()['data']['id'];

        $data['title'] = 'Modified';
        $this->actingAs($user)
            ->put(route('vehicles.update', ['vehicle' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('vehicles', $data);

        $this->actingAs($user)
            ->get(route('vehicles.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('vehicles.show', ['vehicle' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('vehicles.show', ['vehicle' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->delete(route('vehicles.destroy', ['vehicle' => $id]));

        $this->assertDatabaseMissing('vehicles', $data);

        $this->actingAs($user)
            ->get(route('vehicles.latestStatus'))
            ->assertJsonStructure(['data' => ['*' => ['id', 'title', 'vin', 'user_id', 'latestStatus']]]);

    }
}
