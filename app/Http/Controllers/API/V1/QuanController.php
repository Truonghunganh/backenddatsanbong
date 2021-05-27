<?php

namespace App\Http\Controllers\API\V1;

use Symfony\Component\HttpFoundation\Response;
use App\Services\QuanService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CheckTokenService;
use Illuminate\Support\Facades\Validator;
use App\Models\Models\Quan;

class QuanController extends Controller
{
    protected   $quanService;
    protected $checkTokenService;
    public function __construct(QuanService $quanService,CheckTokenService $checkTokenService){
        $this->quanService = $quanService;
        $this->checkTokenService = $checkTokenService;
    }
    public function getListQuansByTrangthaiChoHome(Request $request){
        return response()->json([
            'status' => true,
            'code' => Response::HTTP_OK,
            'quans' => $this->quanService->getAllQuansByTrangthai(1)
        ]);   
        
    }
    public function index(Request $request)
    { 
        try {
           $checkTokenUser=$this->checkTokenService->checkTokenUser($request);
           if ($checkTokenUser) {
                $quans = $this->quanService->getListQuansByTrangthai(Quan::ACTIVE_QUAN, $checkTokenUser->id);
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'quans' => $quans,
                 ]);   
           } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' =>"token sai"
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
    // public function getAllQuanDangHoatdongByUser(Request $request)
    // {
    //     try {
    //         $checkTokenUser = $this->checkTokenService->checkTokenUser($request);
    //         if ($checkTokenUser) {
    //             $quans = $this->quanService->getAllQuansByTrangthai(Quan::ACTIVE_QUAN);
    //             return response()->json([
    //                 'status' => true,
    //                 'code' => Response::HTTP_OK,
    //                 'quans' => $quans
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
    //                 'message' => "token sai"
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
    
    public function getQuanByIdAndTokenInnkeeper(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'idquan' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }
           
            $token = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($token) {
                $id=$request->get('idquan');
                $quan = $this->quanService->findById($id);
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy idquan =" . $id
                    ]);
                }
                if ($token->phone != $quan->phone) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "bạn không có quyền truy cập đến quán này"
                    ]);
                }
                
                return response()->json([
                    'status'  => true,
                    'code'    => Response::HTTP_OK,
                    'quan' => $quan,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token Innkeeper không đúng"
                ]);
            }
        } catch (\Exception $e1) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e1->getMessage()
            ]);
        }
    }
    public function getQuanByIdAndTokenUser(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'idquan' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }
            $token = $this->checkTokenService->checkTokenUser($request);
            if ($token) {
                $idquan = $request->get("idquan");
                $quan = $this->quanService->findByIdVaTrangThai($idquan, Quan::ACTIVE_QUAN);
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy idquan =" . $idquan
                    ]);
                }
                return response()->json([
                    'status'  => true,
                    'code'    => Response::HTTP_OK,
                    'quan' => $quan,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token user không đúng"
                ]);
            }
        } catch (\Exception $e1) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e1->getMessage()
            ]);
        }

    }
    public function show(Request $request,$id)
    {
        try {
            $token = $this->checkTokenService->checkTokenAdmin($request);
            if ($token) {
                $quan = $this->quanService->findById($id);
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy idquan =" . $id
                    ]);
                }
                return response()->json([
                    'status'  => true,
                    'code'    => Response::HTTP_OK,
                    'quan' => $quan,
                    'person' => 'admin'

                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token Admin không đúng"
                ]);
            }
        } catch (\Exception $e1) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e1->getMessage()
            ]);            
        }
    }
    public function getListQuansDaPheDuyetByTokenAdmin(Request $request)
    {
        try {
            $admin = $this->checkTokenService->checkTokenAdmin($request);
            if ($admin) {
                $quans= $this->quanService->getAllQuansByTrangthai(Quan::ACTIVE_QUAN);
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'quans' => $quans,
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
    public function destroy(Request $request,$id){
        try {
            $admin = $this->checkTokenService->checkTokenAdmin($request);
            if ($admin) {
                $quan= $this->quanService->findById($id);
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy quán có id =".$id
                    ]);
                }

                if (!$this->quanService->deleteQuanByAdmin($id,$quan->image)) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "xóa quán không thành công"
                    ]);
                }
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'message' =>"đã xóa quán thành công có id = " . $id
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
    public function UpdateTrangThaiQuanTokenAdmin(Request $request)
    {
        try {
            
            $admin = $this->checkTokenService->checkTokenAdmin($request);
            if ($admin)  {
                $validator = Validator::make($request->all(), [
                    'trangthai' => 'required',
                    'idquan'=>'required'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => $validator->errors()
                    ]);
                }

                $quan= $this->quanService->UpdateTrangThaiQuanTokenAdmin($request);
                if(!$quan){
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "thay đổi trạng thái quán không thành công"
                    ]);
                }
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'quan' =>  $quan
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
    
    public function getListQuansChuaPheDuyetByTokenAdmin(Request $request)
    {
        try {
            $admin = $this->checkTokenService->checkTokenAdmin($request);
            if ($admin) {
                $soluong = $request->get('soluong') ?? 5;
                $quans = $this->quanService->getListQuansByTrangthaiVaPage(Quan::INACTIVE_QUAN, $soluong);
                 return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'quans' => $quans->items(),
                    'tongpage' => $quans->lastPage()
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
       
    public function getListQuansByTokenInnkeeper(Request $request){
        try {
            $innkeeper = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($innkeeper) {
                 
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'quans' =>  $this->quanService->getListQuansByTokenInnkeeper($innkeeper,1)                
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
    public function getListQuansByTokenInnkeeperChuaPheDuyet(Request $request){
        try {
            $innkeeper = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($innkeeper) {

                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                'quans' =>  $this->quanService->getListQuansByTokenInnkeeper($innkeeper, Quan::INACTIVE_QUAN)
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
    public function deleteQuanChuaduyetByInnkeeper(Request $request){
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
            $token = $this->checkTokenService->checkTokenInnkeeper($request);
            if($token)  {
                $quan= $this->quanService->findQuanChuaduyetById($request->get('idquan'));
                if (count($quan)==0) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không có idquan hoặc idquan này bạn không quyền  xóa"
                    ]);
                }
                if($token->phone==$quan[0]->phone){
                    if ($this->quanService->deleteQuanById($request->get("idquan"))) {
                        return response()->json([
                            'status' => true,
                            'code' => Response::HTTP_OK,
                            'message' => "xóa thành  công có id quán là " . $request->get("idquan")
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                            'message' => "xóa không thành công "
                        ]);
                    }
                    
                     
                }else {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "token không có quyền xóa quán này "
                    ]);
                }
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
    public function addQuanByInnkeeper(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'address' => 'required',
                'image' => 'required',
                'linkaddress' => 'required',
                'vido'=> 'required',
                'kinhdo' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }
            $token = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($token) {
                if ($request->hasFile('image')) {
                    $quan = $this->quanService->addQuanByInnkeeper($request, $token);
                    if ($quan) {
                        return response()->json([
                            'status' => true,
                            'code' => Response::HTTP_OK,
                            'message' => "add quan thành công"
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                            'message' => "thêm thất bại"
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không có image"
                    ]);
                }
      
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
    public function editQuanByTokenInnkeeper(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required',
                'address' => 'required',
                'linkaddress' => 'required',
                'vido' => 'required',
                'kinhdo' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }
            $token = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($token) {
                $getQuanById=$this->quanService->findById($request->get('id'));
                if ($getQuanById)  {
                    if ($getQuanById->phone==$token->phone) {
                    
                        $quan = $this->quanService->editQuanByTokenInnkeeper($request, $getQuanById);
                        if ($quan) {
                            return response()->json([
                                'status' => true,
                                'code' => Response::HTTP_OK,
                                'message' => "chỉnh sữa thành công",
                                'quan'=>$quan
                            ]);
                        } else {
                            return response()->json([
                                'status' => false,
                                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                                'message' => "chỉnh sữa thất bại",
                                'quan' => $quan
                            ]);
                        }    
                    } else {
                        return response()->json([
                            'status' => false,
                            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                            'message' =>"token này không có quyền để chỉnh sữa quán đó"
                        ]);    
                    
                    }
                    
                }else {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "id đó không tồn tại"
                    ]);    
                    
                }
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
    public function searchListQuans(Request $request)
    {
        try {
            $quans = $this->quanService->searchListQuans(Quan::ACTIVE_QUAN,$request->get("search"));
            if (count($quans) == 0) {
                $quans = $this->quanService->searchListQuans1(Quan::ACTIVE_QUAN,$request->get("search"));
            }
            return response()->json([
                'status'  => true,
                'code'    => Response::HTTP_OK,
                'quans' => $quans
            ]);
        } catch (\Exception $e1) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e1->getMessage()
            ]);
        }

    }   
}
