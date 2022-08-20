<?php

namespace Tests\Unit;

use App\Models\JobNumberAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobNumberAlertApiTest extends TestCase
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

        $data = JobNumberAlert::factory()->make()->attributesToArray();


        $response = $this->actingAs($user)
            ->post(route('jobNumberAlerts.store'), $data);

        unset($data['receivers']);

        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('job_number_alerts', $data);

        $id = $response->json()['data']['id'];

        $data['no_of_jobs'] = 4;
        $this->actingAs($user)
            ->put(route('jobNumberAlerts.update', ['jobNumberAlert' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('job_number_alerts', $data);

        $this->actingAs($user)
            ->get(route('jobNumberAlerts.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('jobNumberAlerts.show', ['jobNumberAlert' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('jobNumberAlerts.show', ['jobNumberAlert' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->delete(route('jobNumberAlerts.destroy', ['jobNumberAlert' => $id]));

        $this->assertDatabaseMissing('job_number_alerts', $data);


    }
}
