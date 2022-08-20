<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
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

        $data = Category::factory()->make()->attributesToArray();

        unset($data['sf_id']);
        $response = $this->actingAs($user)
            ->post(route('categories.store'), $data);

        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('categories', $data);

        $id = $response->json()['data']['id'];

        $data['name'] = 'Modified';
        $this->actingAs($user)
            ->put(route('categories.update', ['category' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('categories', $data);

        $this->actingAs($user)
            ->get(route('categories.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('categories.show', ['category' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('categories.show', ['category' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->delete(route('categories.destroy', ['category' => $id]));

        $this->assertDatabaseMissing('categories', $data);

    }
}
