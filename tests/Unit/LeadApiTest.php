<?php

namespace Tests\Unit;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadApiTest extends TestCase
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

        $data = Lead::factory()->make()->attributesToArray();

        $response = $this->actingAs($user)
            ->post(route('leads.store'), $data);

        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('leads', $data);

        $id = $response->json()['data']['id'];

        $data['gclid'] = 'Modified';
        $this->actingAs($user)
            ->put(route('leads.update', ['lead' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('leads', $data);

        $this->actingAs($user)
            ->get(route('leads.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('leads.show', ['lead' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('leads.show', ['lead' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->delete(route('leads.destroy', ['lead' => $id]));

        $this->assertDatabaseMissing('leads', $data);


    }
}
