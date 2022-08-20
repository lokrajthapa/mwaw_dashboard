<?php

namespace Tests\Unit;

use App\Models\FlexRecording;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlexRecordingApiTest extends TestCase
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

        $data = FlexRecording::factory()->make()->attributesToArray();

        $response = $this->actingAs($user)
            ->post(route('flexRecordings.store'), $data);

        unset($data['json']);

        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('flex_recordings', $data);

        $id = $response->json()['data']['id'];

        $data['sid'] = '6546sdfsdf5s46f5';
        $this->actingAs($user)
            ->put(route('flexRecordings.update', ['flexRecording' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('flex_recordings', $data);

        $this->actingAs($user)
            ->get(route('flexRecordings.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('flexRecordings.show', ['flexRecording' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('flexRecordings.show', ['flexRecording' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->delete(route('flexRecordings.destroy', ['flexRecording' => $id]));

        $this->assertDatabaseMissing('flex_recordings', $data);


    }
}
