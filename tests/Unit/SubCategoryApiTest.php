<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Status;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubCategoryApiTest extends TestCase
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

        $data = SubCategory::factory()->make()->attributesToArray();

        unset($data['sf_id']);
        $response = $this->actingAs($user)
            ->post(route('subCategories.store'), $data);

        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('sub_categories', $data);

        $id = $response->json()['data']['id'];

        $data['name'] = 'Modified';
        $this->actingAs($user)
            ->put(route('subCategories.update', ['subCategory' => $id]), $data)
            ->assertJson(['data' => [
                'id' => $id,
                ...$data
            ]]);

        $this->assertDatabaseHas('sub_categories', $data);

        $this->actingAs($user)
            ->get(route('subCategories.index'))
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->actingAs($user)
            ->get(route('subCategories.show', ['subCategory' => $id]))->assertOk();

        $this->actingAs($user)
            ->get(route('subCategories.show', ['subCategory' => $id]))->assertJson(['data' => $data]);


        $this->actingAs($user)
            ->delete(route('subCategories.destroy', ['subCategory' => $id]));

        $this->assertDatabaseMissing('sub_categories', $data);

    }
}
