<?php


namespace App\Http\Controllers\API\V1;

use Symfony\Component\HttpFoundation\Response;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CheckTokenService;

class UserController extends Controller
{
    protected $userService;
    protected $checkTokenService;
    public function __construct(UserService $userService,CheckTokenService $checkTokenService)
    {
        $this->userService = $userService;
        $this->checkTokenService = $checkTokenService;
    }
    public function thayDoiTrangThaiUser(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'iduser' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }
            $id=$request->get('iduser');
            $admin = $this->checkTokenService->checkTokenAdmin($request);
            if ($admin) {
                $user = $this->userService->findById($id);
                if (!$user) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy có id =" . $id
                    ]);
                }
                if ($user->role != 'user') {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "đây không phải là người dùng"
                    ]);
                }
                $trangthai=!$user->trangthai;
                $thaidoi= $this->userService->thayDoiTrangThaiUser($id, $trangthai);
                if ($thaidoi) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => $thaidoi
                    ]);
                }
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'message' => "đã thay đổi trạng thái thành công có id = " . $id
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token sai"
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
    public function destroy(Request $request, $id)
    {
        try {
            $admin = $this->checkTokenService->checkTokenAdmin($request);
            if ($admin) {
                $user = $this->userService->findById($id);
                if (!$user) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy có id =" . $id
                    ]);
                }
                if ($user->role=='admin') {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không thể xóa được"
                    ]);
                }
                if ($this->userService->deleteUserByAdmin($id)) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "xóa không thành công"
                    ]);
                }
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'message' => "đã xóa thành công có id = " . $id
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token sai"
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
    
    public function searchUsersByAdmin(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'search'=> 'required',
                'role' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }

            $tonken = $this->checkTokenService->checkTokenAdmin($request);
            if ($tonken) {
                // dd($request->all());
                $users = $this->userService->searchUsersByAdmin($request->get('role'),$request->get('search'));
            
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'users' =>  $users,
                    'role' => $request->get('role')
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token bị sai"
                ]);
            }
        } catch (\Exception $e) {
            $this->checkddatsan = true;
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function index(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }

            $tonken = $this->checkTokenService->checkTokenAdmin($request);
            if ($tonken) {
                $soluong = $request->get('soluong') ?? 10;
                $users = $this->userService->getUserByAdmin($request->get("user"), $soluong);
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'users' =>  $users->items(),
                    'tongpage' => $users->lastPage()
                ]);                
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token bị sai"
                ]);
            }
        } catch (\Exception $e) {
            $this->checkddatsan = true;
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function logout(){
        Auth::logout();
    }
    public function register(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'role' => 'required',
                'name' => 'required',
                'address'=> 'required',
                'gmail' => 'required',
                'phone' => 'required|min:8',
                'password' => 'required|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' =>"thông tin chưa đúng"
                ]);
            }
            
            //var_dump()
            $role=$request->get('role');
            if ($role=="innkeeper"||$role=="user") {
                $user = $this->userService->getUserByPhone($request->get('phone'));
                if ($user) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "số điên thoại đã tôn tại không thể đăng ký được"
                    ]);
                }
                $user = $this->userService->register($request);
                if ($user) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "Đăng ký thất bại"
                    ]);
                }
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'message' => "bạn đã đăng kí thành công "
                ]);
                
            }
            return response()->json([
                'status' => false,
                'role' => $role,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => "không đúng vai trò"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }

    // public function registerUser(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'phone' => 'required|min:8',
    //             'password' => 'required|min:8',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
    //                 'message' => $validator->errors()
    //             ]);
    //         }
    //         $user = $this->userService->registerUser($request);
    //         if ($user) {
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
    //                 'message' => "số điên thoại đã tôn tại không thể đăng ký được"
    //             ]);
    //         }
    //         return response()->json([
    //             'status' => true,
    //             'code' => Response::HTTP_OK,
    //             'message' => "bạn đã đăng kí thành công "
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }


    public function loginUser(Request $request)
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
            $login=[
                'role' => 'user',
                'phone'=>$request->get('phone'),
                'password'=> $request->get('password')
            ];
            if (Auth::attempt($login)) {
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'message' => "đăng nhập thành công ",
                    'token' =>$this->userService->getTokenUser($request,"user")
                ]);
            }
            else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "đăng nhập thất bại"
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
    public function editUserByAdmin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required',
                'phone' => 'required|min:8',
                'gmail' => 'required',
                'address' => 'required',
                'password' => 'required|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }
            $checktoken = $this->checkTokenService->checkTokenAdmin($request);
            if ($checktoken) {
                $user= $this->userService->editUserByAdmin($request,$request->get('id'));
                if ($user) {
                    return response()->json([
                        'status' => true,
                        'code' => Response::HTTP_OK,
                        'message'=>"chỉnh sữa thành công"
                    ]);    
                }else {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "chỉnh sữa thất bại"
                    ]);    
                }
                
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token not found"
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

     public function editUserByToken(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'gmail' => 'required',
                'address' =>'required',
                'password' => 'required|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
                
            }
            $checktoken = $this->checkTokenService->checkTokenUser($request);
            if ($checktoken) {
                $tonken= $this->userService->editUserByToken($request, $checktoken->id);
                if (!$tonken) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "chỉnh sữa đã bị lỗi"
                    ]);    
                }
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'token' => $tonken
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' =>"token của bạn không đúng"
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

}
