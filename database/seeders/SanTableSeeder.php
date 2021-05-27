<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class SanTableSeeder extends Seeder
{
    public function run()
    {
        $tiens5=[130000,140000, 150000, 160000];
        $tiens11 = [300000, 310000, 320000, 330000];
        $tiens7= [200000, 210000, 220000, 230000];
        for ($i=1; $i <10 ; $i++) {
            $data = [
                "name" => "Sân A",
                "idquan" => $i,
                "numberpeople" => 11,
                "priceperhour" => $tiens11[mt_rand(0, 3)],
                "trangthai" => true,
                "Create_time" => Carbon::now()
            ];
            DB::table('sans')->insert($data);
            $data = [
                "name" => "Sân B",
                "idquan" => $i,
                "numberpeople" => 5,
                "priceperhour" =>$tiens5[mt_rand(0, 3)],
                "trangthai" => true,
                "Create_time" => Carbon::now()
            ];
            DB::table('sans')->insert($data);
            $data = [
                "name" => "Sân C",
                "idquan" => $i,
                "numberpeople" => 7,
                "priceperhour" => $tiens7[mt_rand(0, 3)],
                "trangthai" => true,
                "Create_time" => Carbon::now()
            ];
            DB::table('sans')->insert($data);
        
        }
                                 
    }
}
