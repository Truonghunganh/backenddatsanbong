<?php

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
//use JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\Models\User;
use App\Services\QuanService;
use Illuminate\Support\Facades\DB;

class UserService
{
    protected $quanService;
    public function __construct(QuanService $quanService){
        $this->quanService = $quanService;
    }

    public function getListDatSanByIduser($request)
    {
        if ($request->get("iduser")) {
            return DB::table('datsans')->where('iduser', $request->get('iduser'));
        }
        return [];
        
    }
    public function findById($id){
        return DB::table('users')->select('id','role','name','phone','gmail','address','trangthai')->where('id', $id)->first();
    }
    
    public function getUserByAdmin($user,$soluong=10)
    {
        return User::select('id', 'name', 'address', 'phone', 'gmail','trangthai','role')->where("role",$user)->paginate($soluong);
    }
    public function searchUsersByAdmin($role,$search){
        $users= User::select(["id", "name", "phone", "gmail", "address","role","trangthai"])->where("role", "=", $role)->where(function($query) use ($search) {
        $query->where('name', 'like', '%' . strtolower($search) . '%')
        ->orWhere("phone", 'like', '%' . strtolower($search) . '%')
        ->orWhere('address', 'like', '%' . strtolower($search) . '%')
        ->orWhere('gmail', 'like', '%' . strtolower($search) . '%');
        })->get();
        return $users;
    }
    public function thayDoiTrangThaiUser($id,$trangthai){
        DB::beginTransaction();
        try {
            DB::update('update users set trangthai = ? where id = ?', [$trangthai,$id]);
            DB::commit();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function deleteUserByAdmin($id){
        DB::beginTransaction();
        try {
            User::find($id)->delete();
            DB::commit();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }

    }
    public function editUserByAdmin($request, $id)
    {
        DB::beginTransaction();
        try {
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d H:i:s');
            $token = "";
            $user= User::where("id", "=", $id)->first();
            if ($user->role=="innkeeper") {
                $this->quanService->suaSoDienThoai($user->phone,$request->get("phone"));
            }
            $user->name=$request->get("name");
            $user->phone=$request->get("phone");
            $user->gmail=$request->get("gmail");
            $user->address=$request->get("address");
            $user->password= bcrypt($request->get("password"));
            $user->Create_time=$time;
            $user->save();
            $user = User::where("id", "=", $id)->first();
            if ($user) {
                $token = JWTAuth::fromUser($user);
                $user->token=$token;
                $user->save();
                
            } else {
                return false;
            }
            DB::commit();
            return $token;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
    
    public function editUserByToken($request,$id)
    {
        DB::beginTransaction();
        try {
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d H:i:s');
            $token = "";
            $user = User::where("id", "=", $id)->first();
            $user->name = $request->get("name");
            $user->gmail = $request->get("gmail");
            $user->address = $request->get("address");
            $user->password = bcrypt($request->get("password"));
            $user->Create_time = $time;
            $user->save();
            $user = User::where("id", "=", $id)->first();
            if ($user) {
                $token = JWTAuth::fromUser($user);
                $user->token = $token;
                $user->save();
            } else {
                return false;
            }
            DB::commit();
            return $token;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }

    }
    
    public function getTokenUser($request,$role){
        return DB::table('users')->where('role', '=', $role)->where('phone', $request->get('phone'))->get()[0]->token;
    }
    public function register($request)
    {
        DB::beginTransaction();
        try {
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d H:i:s');
            $data = [
                "trangthai"=>true,
                "name" => $request->get('name'),
                "role" => $request->get('role'),
                "phone" => $request->get('phone'),
                "gmail" => $request->get('gmail'),
                "address" => $request->get('address'),
                "password" => bcrypt($request->get('password')),
                "Create_time" => $time
            ];
            User::insert($data);
            $user = User::where("phone", "=", $request->get('phone'))->First();
            if ($user) {
                $token = JWTAuth::fromUser($user);
                DB::update('update users set token = ? where phone = ?', [$token, $request->get('phone')]);
            }else {
                return true;
            }
            DB::commit();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
    
    // public function registerUser($request){
    //     $userCheckPhone= User::where('phone','=', $request->get('phone'))->first();
    //     if($userCheckPhone){
    //         return true;
    //     }
    //     date_default_timezone_set("Asia/Ho_Chi_Minh");
    //     $time = date('Y-m-d H:i:s');
         
    //     DB::insert(
    //         'insert into users (role,name,phone,gmail,address,password,Create_time) values (?,?, ?,?, ?,?,?)', 
    //     [
    //         "user",
    //         $request->get('name'),
    //         $request->get('phone'),
    //         $request->get('gmail'),
    //         $request->get('address'),
    //         bcrypt($request->get('password')),
    //         $time

    //     ]);
    //     $user = User::where("phone", "=", $request->get('phone'))->first();
    //     if ($user) {
    //         $token = JWTAuth::fromUser($user);
    //         DB::update(
    //             'update users set token = ? where phone = ?',
    //             [$token, $request->get('phone')]
    //         );
    //     }
    //     return false;
      
    // }
    public  function getUserByPhone($phone)
    {
        return User::where("phone", "=", $phone)->first();
    }

    
       
}
class User1
{
    public $id;
    public $name;
    public $phone;
    public $gmail;
    public $address;
    public function __construct($id, $name, $phone, $gmail, $address)
    {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->gmail = $gmail;
        $this->address = $address;
    }
}