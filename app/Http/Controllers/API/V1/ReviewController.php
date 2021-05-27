<?php

namespace App\Http\Controllers\API\V1;

use Symfony\Component\HttpFoundation\Response;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\QuanService;

use App\Services\CheckTokenService;
class ReviewController extends Controller
{
    protected $reviewService;
    protected $checkTokenService;
    protected $quanService;
    public function __construct(ReviewService $reviewService, CheckTokenService $checkTokenService, QuanService $quanService)
    {
        $this->reviewService = $reviewService;
        $this->checkTokenService = $checkTokenService;
        $this->quanService = $quanService;
    }

    public function reviewByUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'idquan' => 'required',
                'review'=> 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }
            $token = $this->checkTokenService->checkTokenUser($request);
            if ($token)  {
                $quan = $this->quanService->findByIdVaTrangThai($request->get('idquan'),1);
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' =>"quán này không tồn tại"
                    ]);
                }
                $this->reviewService->reviewByUser($request,$token->id);
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'message' => "review thành công"
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
    
        
}
