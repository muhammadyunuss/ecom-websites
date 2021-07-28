<?php

namespace Database\Seeders;

use App\DeliveryAddress;
use Illuminate\Database\Seeder;

class DeliveryAddressTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deliveryRecords = [
            [
                'id'=>1,
                'user_id'=>'1',
                'name'=>'Muhammad Yunus',
                'address'=>'Jl. Cipta Menanggal Blok 67 F',
                'city'=>'Surabaya',
                'state'=>'Gayungan',
                'country'=>'Indonesia',
                'pincode'=>'11000',
                'mobile'=> '082139661296',
                'status'=>1
            ],
            [
                'id'=>2,
                'user_id'=>'1',
                'name'=>'Muhammad Yunus',
                'address'=>'Jl. Rusun Menanggal',
                'city'=>'Surabaya',
                'state'=>'Gayungan',
                'country'=>'Indonesia',
                'pincode'=>'11000',
                'mobile'=> '082139661296',
                'status'=>1
            ]
        ];

        DeliveryAddress::insert($deliveryRecords);
    }
}
