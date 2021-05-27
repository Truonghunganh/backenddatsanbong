<?php

namespace App\Services;

use App\Models\Models\User;
use App\Models\Models\Quan;

class CheckTokenService
{
    public function getTokenByPhone($request, $role)
    {
        $user = User::where('role', '=', $role)->where('phone', $request->get('phone'))->first();
        if ($user) {
            return $user->token;
        } else {
            return true;
        }
         
    }
    public function getUserByPhone($phone)
    {
        $user = User::where('phone', $phone)->first();
        if ($user) {
            return $user;
        } else {
            return true;
        }
    }
    public function checkTokenUser($request)
    {
        try {
            $token = $request->header('token');
            if (!$token) {
                return false;
            }
            return User::select("id","name", "phone", "gmail","address", "role")->where('role', '=', "user")->where('token', $token)->first();
        
        } catch (\Exception $e) {
            return false;
        }
    }
    public function checkToken($request)
    {
        try {
            $token = $request->header('token');
            if (!$token) {
                return false;
            }
            return User::select("id", "name","phone","gmail","address", "role")-> where('token',"=", $token)->first();
        } catch (\Exception $e) {
            return false;
        }
    }
    public function checkTokenAdmin($request)
    {
        try {
            $token = $request->header('token');
            if (!$token) {
                return false;
            }
            return User::select("id", "name", "phone", "gmail","address", "role")->where('role', '=', "admin")->where('token', $token)->first();
        } catch (\Exception $e) {
            return false;
        }
    
    }


    public function checkTokenInnkeeper($request)
    {

        try {
            $token = $request->header('token');
            if (!$token) {
                return false;
            }
            return User::select("id", "name", "phone", "gmail", "address", "role")->where('role', '=', "innkeeper")->where('token', $token)->first();
        } catch (\Exception $e) {
            return false;
        }
    
    }

    public function checkTokenInnkeeperAndIdquan($request)
    {
        try {
            $token = $request->header('token');
            if (!$token) {
                return false;
            }
            $token = $this->checkTokenInnkeeper($request);
            if ($token) {
                $quan = Quan::where("id", $request->get('idquan'))->first();
                if ($quan) {
                    if ($token->phone == $quan->phone) {
                        return $token;
                    }
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }

     }
}
