<?php

namespace Tests\Unit;

use App\Models\Sms;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsApiTest extends TestCase
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

        $data = Sms::factory()->make([
            'job_id' => 'Example',
            'body' => 'A
                JOB ID# Example
                Name: Example
                Phone: (8777711892 #7111)
                Address: 44 Berl Ave, Etobicoke, Ontario M8Y 3C4
                JobType: Appliances
                Notes: Micro is not working
                $79+$110 labor +parts informed
                s1j.co/j/n-KJZMI2'
        ])->attributesToArray();
        unset($data['email_sent']);
        unset($data['data']);
        $response = $this->actingAs($user)
            ->post(route('sms.store'), $data);
        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('sms', $data);

        $id = $response->json()['data']['id'];

        $data['from'] = 'Modified';
        $this->actingAs($user)
            ->put(route('sms.update', ['sms' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('sms', $data);

        $this->actingAs($user)
            ->get(route('sms.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('sms.show', ['sms' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('sms.show', ['sms' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->delete(route('sms.destroy', ['sms' => $id]));

        $this->assertDatabaseMissing('sms', $data);

    }
}
