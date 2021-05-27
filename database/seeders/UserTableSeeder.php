<?php

namespace Database\Seeders;
use App\Models\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    public function run()
    {
       // user
        $diachis=["quảng nam","đà nẵng","Huế","quảng Bình","quảng Trị","quảng Ngãi",
            "phú yên","Bình định","Nha Trang ","khánh hòa","hồ chí minh"];
        $phones=["0374894100", "0374894101", "0374894102", "0374894103", "0374894104", "0374894105", "0374894106", "0374894107", "0374894108", "0374894109", "0374894110"];
        $tens=["trương ngọc hào", "ngô trí đạt", "nguyễn hoàng", "đinh văn Huy", "nguyễn hoàng Huy",
        "phậm đình điệp", "nguyễn hoàng Phi","nguyễn đình trường","trần anh thư","ngô thị thúy hiền","nguyễn thái học"];
        for ($i=0; $i < 11; $i++) {
            $data = [
                "name" => $tens[$i],
                "role" => "user",
                "phone" => $phones[$i],
                "gmail" => $phones[$i]."@gmail.com",
                "address" => $diachis[$i],
                "password" => bcrypt($phones[$i]),
                "Create_time" => Carbon::now()
            ];
            User::insert($data);
            $user = User::where("phone", "=", $phones[$i])->get();
            if (count($user) > 0) {
                $token = JWTAuth::fromUser($user[0]);

                DB::update('update users set token = ? where phone = ?', [$token, $phones[$i]]);
            }
                
        }
        $phone = "0374894176";
        
        $data=[
            "name"=>"Dương huỳnh quang",
            "role"=>"user",
            "phone"=>$phone,
            "gmail"=>"quang@gmail.com",
            "address"=>"thành Phố thừa thiên huế",
            "password" => bcrypt($phone),
            "Create_time" => Carbon::now()
        ];
        User::insert($data);
        $user = User::where("phone", "=", $phone)->get();
        if (count($user) > 0) {
            $token = JWTAuth::fromUser($user[0]);
            
            DB::update('update users set token = ? where phone = ?', [$token, $phone]);
            
        }
        $phone = "0796234625";

        $data = [
            "name" => "Nguyễn Đình Hân",
            "role" => "user",
            "phone" => $phone,
            "gmail" => "han@gmail.com",
            "address" => "điện Phước -điện bàn-quảng nam",
            "password" =>bcrypt ($phone),
            "Create_time"=> Carbon::now()
        ];
        User::insert($data);
        $user = User::where("phone", "=", $phone)->get();
        if (count($user) > 0) {
            $token = JWTAuth::fromUser($user[0]);
            DB::update('update users set token = ? where phone = ?', [$token, $phone]);
            
        }
        // innkeeper
        //1
        $data = [
            "name" => "Trương Hùng Anh",
            "role" => "innkeeper",
            "phone" => "0812250590",
            "gmail" => "hunganh250590@gmail.com",
            "address" => "nhị dinh 3-điện Phước -điện bàn-quảng nam",
            "password" => bcrypt("0812250590"),
            "Create_time" => Carbon::now()
        ];
        User::insert($data);
        $user = User::where("phone", "=", "0812250590")->get();
        if (count($user) > 0
        ) {
            $token = JWTAuth::fromUser($user[0]);
            DB::update('update users set token = ? where phone = ?', [$token, '0812250590']);
        }
        //2
        $phone = "0354658717";
        $data = [
            "name" => "Võ đức hùng sơn",
            "role" => "innkeeper",
            "phone" => $phone,
            "gmail" => "son@gmail.com",
            "address" => "thành Phố thừa thiên huế",
            "password" => bcrypt($phone),
            "Create_time" => Carbon::now()
        ];
        User::insert($data);
        $user = User::where("phone", "=", $phone)->get();
        if (count($user) > 0) {
            $token = JWTAuth::fromUser($user[0]);

            DB::update('update users set token = ? where phone = ?', [$token, $phone]);
        }
        //3
        $phone = "0337265910";
        $data = [
            "name" => "nguyễn thái quyên",
            "role" => "innkeeper",
            "phone" => $phone,
            "gmail" => "quyen@gmail.com",
            "address" => "Điện hòa-điện bàn-quảng nam",
            "password" => bcrypt($phone),
            "Create_time" => Carbon::now()
        ];
        User::insert($data);
        $user = User::where("phone", "=", $phone)->get();
        if (count($user) > 0) {
            $token = JWTAuth::fromUser($user[0]);

            DB::update('update users set token = ? where phone = ?', [$token, $phone]);
        }
        // 4
        $phone ="0356899335";
        $data = [
            "name" => "Trương Ngọc Hào",
            "role" => "innkeeper",
            "phone" => $phone,
            "gmail" => "Hao@gmail.com",
            "address" => "thành Phố thừa thiên huế",
            "password" => bcrypt($phone),
            "Create_time" => Carbon::now()
        ];

        User::insert($data);
        $user = User::where("phone", "=",$phone)->get();
        if (count($user) > 0
        ) {
            $token = JWTAuth::fromUser($user[0]);

            DB::update('update users set token = ? where phone = ?', [$token, $phone]);
        }
        // 5
        $phone = "0356899336";
        $data = [
            "name" => "Trương Ánh Diệu",
            "role" => "innkeeper",
            "phone" => $phone,
            "gmail" => "anhdieu@gmail.com",
            "address" => "thành Phố thừa thiên huế",
            "password" => bcrypt($phone),
            "Create_time" => Carbon::now()
        ];

        User::insert($data);
        $user = User::where("phone", "=", $phone)->get();
        if (
            count($user) > 0
        ) {
            $token = JWTAuth::fromUser($user[0]);

            DB::update('update users set token = ? where phone = ?', [$token, $phone]);
        }
        // 6
        $phone = "0787179937";
        $data = [
            "name" => "Trương Phú Một",
            "role" => "innkeeper",
            "phone" => $phone,
            "gmail" => "phumot@gmail.com",
            "address" => "thành Phố thừa thiên huế",
            "password" => bcrypt($phone),
            "Create_time" => Carbon::now()
        ];

        User::insert($data);
        $user = User::where("phone", "=", $phone)->get();
        if (
            count($user) > 0
        ) {
            $token = JWTAuth::fromUser($user[0]);

            DB::update('update users set token = ? where phone = ?', [$token, $phone]);
        }
        // 7
        $phone = "0935291246";
        $data = [
            "name" => "Trần Thị Lệ Hương",
            "role" => "innkeeper",
            "phone" => $phone,
            "gmail" => "huong@gmail.com",
            "address" => "thành Phố thừa thiên huế",
            "password" => bcrypt($phone),
            "Create_time" => Carbon::now()
        ];

        User::insert($data);
        $user = User::where("phone", "=", $phone)->get();
        if (
            count($user) > 0
        ) {
            $token = JWTAuth::fromUser($user[0]);

            DB::update('update users set token = ? where phone = ?', [$token, $phone]);
        }

        $phones=["0374894200", "0374894201", "0374894202", "0374894203", "0374894204"];
        for ($i = 0; $i < 5; $i++) {
            $data = [
                "name" => $tens[$i],
                "role" => "innkeeper",
                "phone" => $phones[$i],
                "gmail" => $phones[$i] . "@gmail.com",
                "address" => $diachis[$i],
                "password" => bcrypt($phones[$i]),
                "Create_time" => Carbon::now()
            ];
            User::insert($data);
            $user = User::where("phone", "=", $phones[$i])->get();
            if (count($user) > 0) {
                $token = JWTAuth::fromUser($user[0]);

                DB::update('update users set token = ? where phone = ?', [$token, $phones[$i]]);
            }
        }
        

        // admin
        $phone= "6574839201";
        $data = [
            "name" => "đồ thị minh thúy",
            "role" => "admin",
            "phone" => $phone,
            "gmail" => "thuy@gmail.com",
            "address" => "Điện hòa-điện bàn-quảng nam",
            "password" => bcrypt($phone),
            "Create_time" => Carbon::now()
        ];
        User::insert($data);
        $user = User::where("phone", "=", $phone)->get();
        if (count($user) > 0) {
            $token = JWTAuth::fromUser($user[0]);

            DB::update('update users set token = ? where phone = ?', [$token, $phone]);
        }

    }
}
