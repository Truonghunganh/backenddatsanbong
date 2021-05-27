<?php

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\Models\User;

use Illuminate\Support\Facades\DB;

class ChuQuanService
{
    public function registerInnkeeper($request)
    {
        DB::beginTransaction();
        try {
            $userCheckPhone = User::where('phone', '=', $request->get('phone'))->first();
            if ($userCheckPhone) {
                return true;
            }
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d H:i:s');
            DB::insert(
                'insert into users (role,name,phone,gmail,address,password,Create_time) values (?,?, ?,?, ?,?,?)',
                [
                    "innkeeper",
                    $request->get('name'),
                    $request->get('phone'),
                    $request->get('gmail'),
                    $request->get('address'),
                    bcrypt($request->get('password')),
                    $time

                ]
            );
            $user = User::where("phone", "=", $request->get('phone'))->first();
            if ($user) {
                $token = JWTAuth::fromUser($user);
                DB::update(
                    'update users set token = ? where phone = ?',
                    [$token, $request->get('phone')]
                );
            }
            DB::commit();
            return false;
            
        } catch (\Exception $e) {
            DB::rollBack();
            return true;
            throw new \Exception($e->getMessage());
        }

        
    }

    
    
    

}
