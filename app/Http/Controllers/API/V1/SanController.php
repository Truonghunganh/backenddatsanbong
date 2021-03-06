<?php

namespace App\Http\Controllers\API\V1;

use Symfony\Component\HttpFoundation\Response;
use App\Services\SanService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\QuanService;
use App\Services\CheckTokenService;
use Illuminate\Support\Facades\Validator;
use App\Services\DatSanService;

class SanController extends Controller
{
    protected $sanService;
    protected $checkTokenService;
    protected $quanService;
    protected $datSanService;
    public function __construct(
        SanService $sanService,
        CheckTokenService $checkTokenService, 
        QuanService $quanService,
        DatSanService $datSanService)
    {
        $this->sanService = $sanService;
        $this->checkTokenService=$checkTokenService;
        $this->quanService=$quanService;
        $this->datSanService=$datSanService;
    }
    
    public function index(Request $request)
    {
        try {
            $sans= $this->sanService->getSansByIdquan($request->get('idquan'));
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'san' => $sans,
                'datsans'=> $this->datSanService->getTinhTrangDatSansByIdquanVaNgay($sans,$request->get('start_time')),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
      
    }
    public function thayDoiTrangthaiSanByInnkeeper(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'idsan' => 'required',
                'trangthai' => 'required'
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
                $san = $this->sanService->findById($request->get("idsan"));
                if (!$san) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "kh??ng t??m th???y s??n c?? id =" + $request->get("idsan")
                    ]);
                }
                $quan = $this->quanService->findByIdVaTrangThai($san->idquan, 1);
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "kh??ng t??m th???y qu??n n??y"
                    ]);
                }
                if ($token->phone != $quan->phone) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "b???n kh??ng c?? quy???n truy c???p ?????n qu??n n??y"
                    ]);
                }
                $thayDoiTrangthaiSanByInnkeeper = $this->sanService->thayDoiTrangthaiSanByInnkeeper($request->get('idsan'),$request->get('trangthai'));
                return response()->json([
                    'status'  => true,
                    'code'    => Response::HTTP_OK,
                    'san' => $san,
                    'quan' => $quan
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token Innkeeper kh??ng ????ng"
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
    public function getSanByInnkeeperVaId(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'idsan' => 'required',
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
                $san= $this->sanService->findById($request->get("idsan"));
                if (!$san) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "kh??ng t??m th???y s??n c?? id ="+$request->get("idsan")
                    ]);
                }
                $quan= $this->quanService->findByIdVaTrangThai($san->idquan, 1);
                if (!$quan) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "kh??ng t??m th???y qu??n n??y"
                    ]);
                }
                if ($token->phone!=$quan->phone) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "b???n kh??ng c?? quy???n truy c???p ?????n qu??n n??y"
                    ]);
                }

                return response()->json([
                    'status'  => true,
                    'code'    => Response::HTTP_OK,
                    'san' => $san,
                    'quan' =>$quan
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "token Innkeeper kh??ng ????ng"
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
    public function show(Request $request, $id)
    {
        try {
            if ($request->header('tokenUser')) {
                try {
                    $token = $this->checkTokenService->checkTokenUser($request);
                    if ($token) {
                        return response()->json([
                            'status'  => true,
                            'code'    => Response::HTTP_OK,
                            'san' => $this->sanService->findById($id),
                            'person' => 'user'
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                            'message' => "token user kh??ng ????ng"
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


            if ($request->header('tokenAdmin')) {
                try {
                    $token = $this->checkTokenService->checkTokenAdmin($request);
                    if ($token) {
                        return response()->json([
                            'status'  => true,
                            'code'    => Response::HTTP_OK,
                            'san' => $this->sanService->findById($id),
                            'person' => 'admin'

                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                            'message' => "token Admin kh??ng ????ng"
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
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => "kh??ng c?? token"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }    
    
    public function addSanByInnkeeper(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'idquan' => 'required',
                'name' => 'required',
                'numberpeople' => 'required',
                'priceperhour'=>'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }    
            
            if (!is_int((int)$request->get('priceperhour'))|| !is_int((int)$request->get('numberpeople'))) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "numberpeople(". $request->get('priceperhour').") v?? priceperhour (". $request->get('numberpeople').") y??u c???u ph???i l?? s???"
                ]);
                
            }
            $token = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($token) {
                $quan=$this->quanService->findById($request->get("idquan"));
                if ($quan) {
                    if($token->phone==$quan->phone){
                        $san=$this->sanService->addSanByInnkeeper($request);
                        if ($san==true) {
                            return response()->json([
                                'status' => true,
                                'code' => Response::HTTP_OK,
                                'message' => "add s??n th??nh c??ng",
                                'san' => $san
                            ]);

                        } else {
                            return response()->json([
                                'status' => false,
                                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                                'message' => $san
                            ]);                            
                        }
                        
                    }else {
                        return response()->json([
                            'status' => false,
                            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                            'message' => "token n??y kh??ng c?? quy???n th??m v??o trong qu??n  n??y"
                        ]);
                
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "idquan kh??ng c??"
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

    public function editSanByInnkeeper(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required',
                'numberpeople' => 'required',
                'priceperhour' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $validator->errors()
                ]);
            }

            if (!is_int($request->get('priceperhour')) || !is_int($request->get('numberpeople'))) {
                return response()->json([
                    'status' => false,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "numberpeople(" . $request->get('priceperhour') . ") v?? priceperhour (" . $request->get('numberpeople') . ") y??u c???u ph???i l?? s???"
                ]);
            }
            $token = $this->checkTokenService->checkTokenInnkeeper($request);
            if ($token) {
                $san=$this->sanService->findById($request->get('id'));
                if (!$san) {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "kh??ng t??m th???y s??n c?? id = " . $request->get('id')
                    ]);                    
                }
                $quan = $this->quanService->findById($san->idquan);
                if ($quan) {
                    if ($token->phone == $quan->phone) {
                        $san = $this->sanService->editSanByInnkeeper($request);
                        if ($san==true) {
                            return response()->json([
                                'status' => true,
                                'code' => Response::HTTP_OK,
                                'message' => "edit s??n th??nh c??ng",
                                'san' => $san
                            ]);
                        } else {
                            return response()->json([
                                'status' => false,
                                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                                'message' => $san
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                            'message' => "token n??y kh??ng c?? quy???n th??m v??o trong qu??n  n??y"
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "idquan kh??ng c??"
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
