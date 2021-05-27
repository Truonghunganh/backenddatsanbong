<?php

namespace App\Http\Controllers\API\V1;

use App\Services\CheckTokenService;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Services\QuanService;
use App\Services\ReviewService;
use App\Services\CommentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $checkTokenService;
    protected $quanService;
    protected $commentService;
    protected $reviewService;
    public function __construct(
        CheckTokenService $checkTokenService,
        QuanService $quanService,
        ReviewService $reviewService,
        CommentService $commentService
    ) {
        $this->checkTokenService = $checkTokenService;
        $this->quanService = $quanService;
         $this->commentService = $commentService;
         $this->reviewService = $reviewService;
    }
    public function getAllCommentCuaMotQuanByAdmin(Request $request){
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

            $tonken = $this->checkTokenService->checkTokenAdmin($request);
            if ($tonken) {
                $comments = $this->commentService->getAllCommentCuaMotQuanByInnkeeper($request->get('idquan'));
                return response()->json([
                    'status'  => true,
                    'code'    => Response::HTTP_OK,
                    'comments' => $comments
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
    public function getAllCommentCuaMotQuanByInnkeeper(Request $request){
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

            $tonken = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($tonken) {
                $quan = $this->quanService->findByIdVaTrangThai($request->get('idquan'), 1);
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy quán này",
                    ]);
                }
                if ($tonken->phone!= $quan->phone) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "bạn không có quyền truy cập đến quán này",
                    ]);
                }
                $comments = $this->commentService->getAllCommentCuaMotQuanByInnkeeper($quan->id);
                return response()->json([
                    'status'  => true,
                    'code'    => Response::HTTP_OK,
                    'comments' => $comments
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
    public function index(Request $request)
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

            $tonkenUser = $this->checkTokenService->checkTokenUser($request);
            if ($tonkenUser) {
                $quan = $this->quanService->findByIdVaTrangThai($request->get('idquan'), 1);
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy quán này",
                    ]);
                }
                $comments = $this->commentService->getAllCommentsCuaMotQuan($quan->id, $tonkenUser);
                return response()->json([
                    'status'  => true,
                    'code'    => Response::HTTP_OK,
                    'comments' => $comments
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
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
               'idquan' => 'required',
               'binhluan' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }

            $tonkenUser = $this->checkTokenService->checkTokenUser($request);
            if ($tonkenUser) {
                $quan = $this->quanService->findByIdVaTrangThai($request->get('idquan'), 1);
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy quán này",
                    ]);
                }
                $comments = $this->commentService->addComment($quan->id, $tonkenUser, $request->get("binhluan"));
                return response()->json([
                    'status'  => true,
                    'code'    => Response::HTTP_OK,
                    'comments' => $comments
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
    public function show($id)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'binhluan' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }

            $tonkenUser = $this->checkTokenService->checkTokenUser($request);
            if ($tonkenUser) {
                $comment = $this->commentService->findById($id);
                if (!$comment) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tim thấy",
                    ]);
                }
                $review = $this->reviewService->findById($comment->idreview);
                if (!$review) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tim thấy",
                    ]);
                }
                if ($tonkenUser->id != $review->iduser) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "bạn không có quyền xóa bình luận này",
                    ]);
                }
                $comments = $this->commentService->updateComment($id, $request->get("binhluan"), $review->idquan, $tonkenUser);
                return response()->json([
                    'status'  => true,
                    'code'    => Response::HTTP_OK,
                    'comments' => $comments
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
    public function destroy($id,Request $request)
    {
        try {
            $user = $this->checkTokenService->checkTokenUser($request);
            if ($user) {
                $comment = $this->commentService->findById($id);
                if (!$comment) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tim thấy",
                    ]);
                }
                $review = $this->reviewService->findById($comment->idreview);
                if (!$review) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tim thấy",
                    ]);
                }
                if ($user->id != $review->iduser) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "bạn không có quyền xóa bình luận này",
                    ]);
                }
                return response()->json([
                    'status'  => true,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'comments' => $this->commentService->deleteComment($id,$review->idquan,$user)
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
}
    