<?php

namespace App\Http\Controllers\API\V1;


use Symfony\Component\HttpFoundation\Response;
use App\Services\AdminService;
use App\Services\UserService;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CheckTokenService;

class AdminController extends Controller
{
    protected $adminService;
    protected $checkTokenService;
    protected $userService;
    public function __construct(AdminService $adminService, CheckTokenService $checkTokenService,UserService $userService)
    {
        $this->adminService = $adminService;
        $this->checkTokenService = $checkTokenService;
        $this->userService = $userService;
    }
    public function loginAdmin(Request $request)
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
                'role' => 'admin',
                'phone' => $request->get('phone'),
                'password' => $request->get('password')
            ];
            if (Auth::attempt($login)) {
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'message' => "đăng nhập thành công ",
                    'token' => $this->checkTokenService->getTokenByPhone($request, "admin")
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

    public function editAdminByToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
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
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'token' => $this->userService->editUserByToken($request, $checktoken->id)
                ]);
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
    
}
