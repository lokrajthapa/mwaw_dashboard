<?php

namespace Tests\Unit;

use App\Models\FlexTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlexTaskApiTest extends TestCase
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

        $data = FlexTask::factory()->make()->attributesToArray();

        $response = $this->actingAs($user)
            ->post(route('flexTasks.store'), $data);

        unset($data['attributes']);

        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('flex_tasks', $data);

        $id = $response->json()['data']['id'];

        $data['sid'] = '6546sdfsdf5s46f5';
        $this->actingAs($user)
            ->put(route('flexTasks.update', ['flexTask' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('flex_tasks', $data);

        $this->actingAs($user)
            ->get(route('flexTasks.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('flexTasks.show', ['flexTask' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('flexTasks.show', ['flexTask' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->delete(route('flexTasks.destroy', ['flexTask' => $id]));

        $this->assertDatabaseMissing('flex_tasks', $data);


    }
}
