<?php

use Illuminate\Database\Seeder;
use App\ProductsImage;

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
            ['id'=> 1, 'product_id'=> 1, 'image'=> 'plain-blue-tshirts-500x500.jpg-9217.jpg', 'status'=> 1,]
        ];

        ProductsImage::insert($productImageRecorcds);
    }
}
