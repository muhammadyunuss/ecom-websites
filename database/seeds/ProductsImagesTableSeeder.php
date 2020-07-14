<?php

use Illuminate\Database\Seeder;
use App\ProducrsImage;

class ProductsImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productImageRecorcds = [
            ['id'=> 1, 'product_id'=> 1, 'image'=> 'Edit 4.png', 'status'=> 1,]
        ];
    }
}
