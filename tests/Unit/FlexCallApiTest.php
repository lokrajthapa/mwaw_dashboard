<?php

namespace Tests\Unit;

use App\Models\FlexCall;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlexCallApiTest extends TestCase
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

        $data = FlexCall::factory()->make()->attributesToArray();

        $response = $this->actingAs($user)
            ->post(route('flexCalls.store'), $data);

        unset($data['json']);

        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('flex_calls', $data);

        $id = $response->json()['data']['id'];

        $data['sid'] = '6546sdfsdf5s46f5';
        $this->actingAs($user)
            ->put(route('flexCalls.update', ['flexCall' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('flex_calls', $data);

        $this->actingAs($user)
            ->get(route('flexCalls.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('flexCalls.show', ['flexCall' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('flexCalls.show', ['flexCall' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->delete(route('flexCalls.destroy', ['flexCall' => $id]));

        $this->assertDatabaseMissing('flex_calls', $data);


    }
}
