<?php

namespace Tests\Unit;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobApiTest extends TestCase
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

        $data = Job::factory()->make()->attributesToArray();

        $response = $this->actingAs($user)
            ->post(route('jobs.store'), $data);
        unset($data['sf_id']);
        unset($data['sf_job_number']);
        unset($data['start_datetime']);
        unset($data['end_datetime']);
        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('jobs', $data);

        $id = $response->json()['data']['id'];

        $data['title'] = 'Modified';
        $data['description'] = 'new description';
        $this->actingAs($user)
            ->put(route('jobs.update', ['job' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('jobs', $data);

        $this->actingAs($user)
            ->get(route('jobs.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('jobs.show', ['job' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('jobs.show', ['job' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->get(route('jobs.bySchedule', ['start_date' => $data['start_date']]))
            ->assertJsonStructure(['data' => ['*' => ['id']]]);


        $this->actingAs($user)
            ->delete(route('jobs.destroy', ['job' => $id]));

        $this->assertDatabaseMissing('jobs', $data);


    }
}
