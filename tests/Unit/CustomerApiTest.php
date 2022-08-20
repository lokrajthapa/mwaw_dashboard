<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerApiTest extends TestCase
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

        $data = Customer::factory()->make()->attributesToArray();

        unset($data['sf_id']);
        $response = $this->actingAs($user)
            ->post(route('customers.store'), $data);


        $response->assertJson(['data' => $data]);

        unset($data['full_name']);
        $this->assertDatabaseHas('customers', $data);

        $id = $response->json()['data']['id'];

        $data['first_name'] = 'ModifiedCustomer';
        $data['email'] = 'modifiedemail@gmail.com';
        $this->actingAs($user)
            ->put(route('customers.update', ['customer' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('customers', $data);

        $this->actingAs($user)
            ->get(route('customers.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('customers.show', ['customer' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('customers.show', ['customer' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->delete(route('customers.destroy', ['customer' => $id]));

        $this->assertDatabaseMissing('customers', $data);

    }
}
