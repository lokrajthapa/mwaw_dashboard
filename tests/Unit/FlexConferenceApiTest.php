<?php

namespace Tests\Unit;

use App\Models\FlexConference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlexConferenceApiTest extends TestCase
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

        $data = FlexConference::factory()->make()->attributesToArray();

        $response = $this->actingAs($user)
            ->post(route('flexConferences.store'), $data);

        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('flex_conferences', $data);

        $id = $response->json()['data']['id'];

        $data['sid'] = '6546sdfsdf5s46f5';
        $this->actingAs($user)
            ->put(route('flexConferences.update', ['flexConference' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('flex_conferences', $data);

        $this->actingAs($user)
            ->get(route('flexConferences.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('flexConferences.show', ['flexConference' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('flexConferences.show', ['flexConference' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->delete(route('flexConferences.destroy', ['flexConference' => $id]));

        $this->assertDatabaseMissing('flex_conferences', $data);

    }
}
