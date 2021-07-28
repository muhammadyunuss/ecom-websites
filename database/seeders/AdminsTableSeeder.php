<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->delete();
        $adminRecords = [
            ['id'=>1,'name'=>'yunus','type'=>'admin','mobile'=>'082139661296','email'=>'yunnusmuhammad@gmail.com','password'=>'$2y$10$d5aOb.P3u/rDyOBBBxTkb.L77h3HnQBY2uZEQKPhToiriA7C/1oyK','image'=>'','status'=>1],
            ['id'=>2,'name'=>'nisa','type'=>'admin','mobile'=>'082139661296','email'=>'nisa@admin.com','password'=>'$2y$10$d5aOb.P3u/rDyOBBBxTkb.L77h3HnQBY2uZEQKPhToiriA7C/1oyK','image'=>'','status'=>1],
            ['id'=>3,'name'=>'admin','type'=>'subadmin','mobile'=>'082139661296','email'=>'admin@subadmin.com','password'=>'$2y$10$d5aOb.P3u/rDyOBBBxTkb.L77h3HnQBY2uZEQKPhToiriA7C/1oyK','image'=>'','status'=>1],
            ['id'=>4,'name'=>'kasir','type'=>'subadmin','mobile'=>'082139661296','email'=>'kasir@subadmin.com','password'=>'$2y$10$d5aOb.P3u/rDyOBBBxTkb.L77h3HnQBY2uZEQKPhToiriA7C/1oyK','image'=>'','status'=>1],
        ];

        DB::table('admins')->insert($adminRecords);
        /*foreach ($adminRecords as $key => $record) {
            \App\Admin::create($record);
        }*/
    }
}
