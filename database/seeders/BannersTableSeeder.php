<?php

use Illuminate\Database\Seeder;
use App\Banner;

class BannersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bannerRecords = [
            ['id' => 1,'image'=>'banner1.png','link'=>'','title'=>'Black Jacket','alt'=>'Black Jacket','status'=>1],
            ['id' => 2,'image'=>'banner2.png','link'=>'','title'=>'Half Sleeve Jacket','alt'=>'Half Sleeve Jacket','status'=>1],
            ['id' => 3,'image'=>'banner3.png','link'=>'','title'=>'Full Sleeve Jacket','alt'=>'Full Sleeve Jacket','status'=>1],
        ];

        Banner::insert($bannerRecords);
    }
}
