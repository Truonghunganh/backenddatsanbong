<?php

namespace App\Http\Controllers\API\V1;

use Symfony\Component\HttpFoundation\Response;
use App\Services\CheckTokenService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SanService;
use App\Services\QuanService;
use Illuminate\Support\Facades\Validator;
use App\Models\Models\DatSan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Providers\Facility;
use App\Settings;

class CheckTokenController extends Controller
{
    protected $settings;
    protected $checkTokenService;
    protected $sanService;
    protected $quanService;
    public function __construct(CheckTokenService $checkTokenService,
            SanService $sanService, 
            QuanService $quanService,
            Settings $settings
            )
    {
        $this->checkTokenService = $checkTokenService;
        $this->sanService = $sanService;
        $this->quanService = $quanService;
        $this->settings = $settings;

    }
    public static $b=1;
    public $a=1;
    public function getB(){
        self::$b++;
        return self::$b;
    }
    public  function  thu(Request $request)
    {
        //return "hùng anh đẹp trai";
        $c=$this->settings->get("checkdatsan")+1;
        $this->settings->put('checkdatsan', $c);
        return $this->settings->get('checkdatsan');

        return $this->getB();
        $this->a= config('app.checkdatsan')+1;
        config(['app.checkdatsan' => $this->a]);
        return config('app.checkdatsan') ;
    
    
    
    
    
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $time = date('Y-m-d H:i:s');
        //  $week = strtotime(date("Y-m-d H:i:s", strtotime($time)) . " -1 days");
        return $week = strftime("%Y-%m-%d %H:%M:%S", strtotime(date("Y-m-d H:i:s", strtotime($time)) . " -1 days"));


         //$config = require('config');

        return  require("key");
        //mt_rand(2, 4);
        // Config::set('site_settings', $site_settings);

        // Config::get('site_settings');
        $a=7;
        return $a/4;
        return mt_rand(0, 2) ;

        return
        DB::table('datsans')->whereIn('idsan', [4])->whereDay('start_time', 13)->whereMonth('start_time', 10)->whereYear('start_time', 2021)->get();
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $time= date('Y-m-d h:i:s');
        return $this->quanService->findById(1);        
        if ($request->get('time')>$time) {
            return $time;
         } else {
            return 0;
    
        }
        
        $today = date('Y-m-d');
        $week = strtotime(date("Y-m-d", strtotime($today)) . " -1 week");
        $week=strftime("%Y-%m-%d", $week);

        //$listds=DatSan::where()
        return response()->json([
            'status' => $today,
            'code' => $week
        ]);
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|min:8',
                'password' => 'required|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }
            $login = [
                'phone' => $request->get('phone'),
                'password' => $request->get('password')
            ];
            if (Auth::attempt($login)) {
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'message' => "đăng nhập thành công ",
                    'user' => $this->checkTokenService->getUserByPhone($request->get("phone"))
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "đăng nhập thất bại vì phone và password không đúng"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function checkToken(Request $request)
    {
        try {
            $checkToken = $this->checkTokenService->checkToken($request);
            if ($checkToken) {
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'user' => $checkToken,
                    'token' =>$request->header('token')
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token bị sai"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function checkTokenUser(Request $request)
    {
        try {
            $checkToken= $this->checkTokenService->checkTokenUser($request);
            if ($checkToken) {
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'user' => $checkToken,
                    'role' =>"user"
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token không đúng",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function checkTokenInnkeeper(Request $request)
    {
        try {
            $checkToken = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($checkToken) {
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'innkeeper' => $checkToken
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token user false"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function checkTokenAdmin(Request $request)
    {
        
        try {
            $checkToken = $this->checkTokenService->checkTokenAdmin($request);
            if ($checkToken) {
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'admin' => $checkToken
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token user false"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => "token user sai"
            ]);
        }
    }
    public function checkTokenInnkeeperAndIdquan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'idquan' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }

            $checkToken = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($checkToken) {
                $quan = $this->quanService->findById($request->get('idquan'));
                if($quan->phone!=$checkToken->phone) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "bạn không có quyền truy cập đến quán này"
                    ]);    
                }
                $user = new User($checkToken->id, $checkToken->name, $checkToken->phone, $checkToken->gmail, $checkToken->address, $checkToken->role);
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'innkeeper' => $user,
                    'quan' =>$quan
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token user false"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function checkTokenInnkeeperAndIdsan(Request $request)
    {
        try {
            $checkToken = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($checkToken) {
                $san=$this->sanService->findById($request->get("idsan"));
                if(!$san) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "id san không tồn tại"
                    ]);
                    
                }
                $quan= $this->quanService->findById($san->idquan);
                if($quan->phone!=$checkToken->phone) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "token không quyền truy cập"
                    ]);
        
                }
                $user = new User($checkToken->id, $checkToken->name, $checkToken->phone, $checkToken->gmail, $checkToken->address,$checkToken->role);
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'innkeeper' => $user
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token user false"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ]);
        }
    }
 
}
class User
{
    public $id;
    public $name;
    public $phone;
    public $gmail;
    public $address;
    public $role;
    public function __construct($id, $name, $phone, $gmail, $address, $role)
    {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->gmail = $gmail;
        $this->address = $address;
        $this->role = $role;
    }
}