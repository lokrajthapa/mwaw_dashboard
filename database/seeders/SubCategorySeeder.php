<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subCategories = [
            [
                'Button Property Management AP',
                'COD AP',
                'Complete Protection AP',
                'Installation AP',
                'Landlord Property Management AP',
                'Miele Billable',
                'Miele Warranty',
                'ORO Properties AP',
                'property M AP',
                'Property Management Toronto AP',
                'Royal York PRMT AP',
                'SOS Warranty',
            ],
            [
                'Button Property Management P',
                'COD P',
                'Landlord Property Management P',
                'ORO Properties P',
                'Property M P',
                'Property Management Toronto P',
                'Royal York PRMT',
            ],
            [
                'Button Property Management H',
                'COD H',
                'Landlord Property Management H',
                'ORO Properties H',
                'Property M H',
                'Property Management Toronto H',
                'Royal York PRMT H',
            ],
            [
                'Button Property Management E',
                'COD E',
                'Landlord Property Management E',
                'ORO Properties E',
                'Property M E',
                'Property Management Toronto E',
                'Royal York PRMT E',
            ]
        ];


        foreach ($subCategories as $index => $subCategory) {
            foreach ($subCategory as $item) {
                SubCategory::create(['category_id' => $index + 1, 'name' => $item]);
            }
        }
    }
}
