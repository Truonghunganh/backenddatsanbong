<?php

namespace App\Http\Controllers\API\V1;

use Symfony\Component\HttpFoundation\Response;
use App\Services\DatSanService;
use App\Services\CheckTokenService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\SanService;
use App\Services\QuanService;
use App\Services\DoanhThuService;
use App\Services\ReviewService;
use App\Settings;

class DatSanController extends Controller 
{
    protected $datSanService;
    protected $sanService;
    protected $checkTokenService;
    protected $doanhThuService;
    protected $reviewService;
    protected $settings;
    private $checkddatsan;
    public function __construct(
        DatSanService $datSanService,
        CheckTokenService $checkTokenService,
        SanService $sanService, 
        QuanService $quanService,
        DoanhThuService $doanhThuService,
        ReviewService $reviewService, 
        Settings $settings
        ){
        $this->datSanService = $datSanService;
        $this->checkTokenService = $checkTokenService;
        $this->sanService = $sanService;
        $this->quanService = $quanService;
        $this->doanhThuService = $doanhThuService;
        $this->reviewService = $reviewService;
        $this->settings = $settings;
    }
    // show là add data lên (để thêm vào)
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [            
                'idsan' => 'required',
                'price'=> 'required',
                'start_time' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d H:i:s');
            if ($request->get('start_time') < $time) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "bạn phải đặt Trước thời gian hiện tại",
                ]);
            }
            
            $tonkenUser=$this->checkTokenService->checkTokenUser($request);
            if($tonkenUser){
                $this->checkddatsan= $this->settings->get("checkdatsan");
                if ($this->checkddatsan) {
                    $this->settings->put('checkdatsan', false);
                    $datsan = $this->datSanService->addDatSan($request, $tonkenUser->id);
                    $this->settings->put('checkdatsan', true);
                    return response()->json([
                        'status'  => true,
                        'code'    => Response::HTTP_OK,
                        'datsan' => $datsan
                    ]);                                                                         
                    if ($datsan) {
                       $this->settings->put('checkdatsan', true);
                        return response()->json([
                            'status'  => true,
                            'code'    => Response::HTTP_OK,
                            'datsan' => $datsan
                        ]);
                    } else {
                        $this->settings->put('checkdatsan', true);
                        return response()->json([
                            'status' => false,
                            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                            'message' => "bạn đã đặt sân thất bại1"
                        ]);
                    }
                } else {
                    $this->settings->put('checkdatsan', true);
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "bạn đã đặt sân thất bại2"
                    ]);
                }
            }
            else {
                $this->settings->put('checkdatsan', true);
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token bị sai"
                ]);
            }
        } catch (\Exception $e) {
            $this->settings->put('checkdatsan', true);
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getListDatSanByUserToken(Request $request){
        try {
            $userbyToken=$this->checkTokenService->checkTokenUser($request);
            if ($userbyToken) {
                $iduser=$userbyToken->id;
                $datsans= $this->datSanService->getListDatSanByIduser($iduser);
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'datsans' => $datsans
                ]);

            }
            else{
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
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getListDatSanByInnkeeper(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_time' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }

            $innkeeper = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($innkeeper) {
                $datsans = $this->datSanService->getListDatSanByInnkeeper($innkeeper,$request->get("start_time"));
                if (count($datsans) == 0) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "id user không tồn tại"
                    ]);
                }
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'datsans' => $datsans
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
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy(Request $request,$id){
        try {
            $user = $this->checkTokenService->checkTokenUser($request);
            if ($user) {
                $datsan = $this->datSanService->find($id);
                if (!$datsan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy id đặt sân này "
                    ]);
                }
                
                if ($user->id != $datsan->iduser) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "token này không có quyền tri cập đến đặt sân này"
                    ]);
                }
                $san= $this->sanService->findById($datsan->idsan);
                if(!$san){
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy sân bởi idsan = ".$datsan->idsan
                    ]);
                }

                date_default_timezone_set("Asia/Ho_Chi_Minh");
                $time = date('Y-m-d H:i:s');
                $time = strftime("%Y-%m-%d %H:%M:%S", strtotime(date("Y-m-d H:i:s", strtotime($time)) . "+1 days"));
                if($time>$datsan->start_time&& $datsan->xacnhan){
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không thể xóa được vì thời gian hủy đặt sân phải trước 1 ngày"
                    ]);
                }
                $ds= $this->datSanService->deleteDatsan($id,$san,$datsan);
                if ($ds) {
                    return response()->json([
                        'status' => true,
                        'code' => Response::HTTP_OK,
                        'message' => "xóa thành công đặt sân",
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "Xóa đặt sân thất bại"
                    ]);
                }
                
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
                'message' => $e->getMessage()
            ]);
        }
    }
    public function xacNhanDatsanByInnkeeper(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'iddatsan' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }

            $innkeeper = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($innkeeper) {
                $datsan=$this->datSanService->getDatSanById($request->get('iddatsan'),false);
                if(!$datsan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy id đặt sân này hoặt đặt sân này đã xác nhận rồi"
                    ]);
                }
                $san=$this->sanService->findById($datsan->idsan);
                if (!$san) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy id sân này = ".$datsan->idsan
                    ]);
                }
                
                $quan = $this->quanService->findById($san->idquan);
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy id quán này = ".$san->idquan
                    ]);
                }
                
                if ($innkeeper->phone != $quan->phone) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "token này không có quyền tri cập đến quán này"
                    ]);
                }
                $xacnhan = $this->datSanService->xacNhanDatsan($datsan,1,$datsan->start_time,$datsan->price,$san);
                if ($xacnhan) {
                    return response()->json([
                        'status' => true,
                        'code' => Response::HTTP_OK,
                        'message' =>"xác nhận thành công",
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "xác nhận thất bại",
                    ]);
                }
                
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
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function getDatSansvaSansByInnkeeperAndIdquanAndNgay(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'idquan' => 'required',
                'start_time' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }

            $innkeeper = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($innkeeper) {
                $quan = $this->quanService->findByIdVaTrangThai($request->get('idquan'),1);
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "quán này không hoạt động"
                    ]);
                }
                if ($innkeeper->phone != $quan->phone) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "token này không có quyền tri cập đến quán này"
                    ]);
                }
                $sans = $this->sanService->getSansByIdquan($request->get('idquan'));
                $sansTT=$this->sanService->getSansByIdquanVaTrangthai($request->get('idquan'),1);
                $datsans = $this-> datSanService->getDatSansByInnkeeperAndIdquanAndNgay($sansTT,  $request->get("start_time"));
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'datsans' => $datsans,
                    'sansTT' => $sansTT,
                    'sans' => $sans,
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
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getDatSansvaSansByUserAndIdquanAndNgay(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'idquan' => 'required',
                'start_time' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }

            $user = $this->checkTokenService->checkTokenUser($request);
            if ($user) {
                $quan = $this->quanService->findByIdVaTrangThai($request->get('idquan'),1);
                if(!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "ban không có quyền truy cập đến quán này"
                    ]); 
                }
                $sans = $this->sanService->getSansByIdquan($request->get('idquan'));
                $sansTT=$this->sanService->getSansByIdquanVaTrangthai($request->get('idquan'), 1);
                $datsans =  $this->datSanService->getTinhTrangDatSansByIdquanVaNgay($sansTT,$request->get('start_time'));            
                $reviewcuauser=0;
                $review= $this->reviewService->findReviewByIduserVaIdquan($user->id, $request->get("idquan"));
                if($review){
                    $reviewcuauser=$review->review;
                }
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'datsans' => $datsans,
                    'sans' => $sans,
                    'quan' => $quan,
                    'sansTT'=>$sansTT,
                    'reviewcuauser'=> $reviewcuauser
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
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getDatSansvaSansByAdminAndIdquanAndNgay(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'idquan' => 'required',
                'start_time' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }

            $admin = $this->checkTokenService->checkTokenAdmin($request);
            if ($admin) {
                $quan = $this->quanService->findById($request->get('idquan'));
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không quán này trong hệ thống"
                    ]);
                 }
                
                $sans = $this->sanService->getSansByIdquan($request->get('idquan'));
                $sansTrangthai=$this->sanService->getSansByIdquanVaTrangthai($request->get('idquan'), 1);
                $datsans = $this->datSanService->getDatSansByInnkeeperAndIdquanAndNgay($sansTrangthai,  $request->get("start_time"));
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'datsans' => $datsans,
                    "sansTT"=>$sansTrangthai,
                    'sans' => $sans,
                    'quan'=>$quan
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
                'message' => $e->getMessage()
            ]);
        }
    }




    public function getAllDatSanByInnkeeperAndIdquan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'idquan' => 'required',
                'trangthai' => 'required',
                'time' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }

            $innkeeper = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($innkeeper)  {
                $quan= $this->quanService->findById($request->get('idquan'));
                if($innkeeper->phone!=$quan->phone){
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "token này không có quyền tri cập đến quán này"
                    ]); 
                }
                $datsans = $this->datSanService->getAllDatSanByIdquan($request->get("idquan"),$request->get("trangthai"),$request->get("time"),">=");
                return response()->json([
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'datsans' => $datsans,
                    'quan'=>$quan
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
                'message' => $e->getMessage()
            ]);
        }
    }

    public function thayDoiDatSanByInnkeeper(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'idsanOld' => 'required',
                'idsanNew' => 'required',
                'timeOld' => 'required',
                'timeNew' => 'required',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d H:i:s');
            if ($time>$request->get('timeOld')||$time>$request->get('timeNew')) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "thời gian hiện tại phải bé hơn thời gian thay đổi đặt sân"
                ]);
            }       
            $token = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($token) {
                $sanOld = $this->sanService->findById($request->get('idsanOld'));
                $sanNew = $this->sanService->findById($request->get('idsanNew'));

                if (!$sanOld) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy sân có id = " . $request->get('idsanOld')
                    ]);
                }
                if (!$sanNew) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "không tìm thấy sân có id = " . $request->get('idsanNew')
                    ]);
                }
                $datsanOld= $this->datSanService->getdatsan($request->get('idsanOld'), $request->get('timeOld'));
                if (!$datsanOld) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "đặt sân củ chưa được đặt nên bạn không thể thay đổi "
                    ]);
                }
                $datsanNew= $this->datSanService->getdatsan($request->get('idsanNew'), $request->get('timeNew'));
                if ($datsanNew) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "đặt sân mới được đặt nên bạn không thể thay đổi "
                    ]);
                }
                $quanOld = $this->quanService->findById($sanOld->idquan);
                $quanNew = $this->quanService->findById($sanNew->idquan);
                if (!$quanOld) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "idquan không có"
                    ]);
                }
                if (!$quanNew) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "idquan không có"
                    ]);
                }

                if ($token->phone == $quanOld->phone&&$quanNew->phone == $quanOld->phone) {
                    $datsan = $this->datSanService->thayDoiDatSanByInnkeeper($request->get('timeOld'),$request->get('timeNew'),$sanOld,$sanNew,$datsanOld);
                    if ($datsan) {
                        return response()->json([
                            'status' => true,
                            'code' => Response::HTTP_OK,
                            'message' => "thay đổi đặt sân thành công"
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                            'message' => "thay đổi đặt sân thất bại"
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "token này không có quyền trong quán  này"
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

}
