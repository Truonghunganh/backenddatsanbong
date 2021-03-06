<?php

namespace App\Services;

use Carbon\Carbon;

use App\Services\CheckTokenService;
use App\Models\Models\DoanhThu;
use App\Services\QuanService;
use App\Services\SanService;
use App\Services\UserService;
use App\Models\Models\San;

use Illuminate\Support\Facades\DB;
use App\Models\Models\Quan;
use App\Models\Models\DatSan;
class DatSanService
{
    protected $checkTokenService;
    protected $quanService;
    protected $sanService;
    protected $userService;
    public function __construct(CheckTokenService $checkTokenService,QuanService $quanService,SanService $sanService,UserService $userService)
    {
        $this->checkTokenService = $checkTokenService;
        $this->quanService = $quanService;
        $this->sanService = $sanService;
        $this->userService = $userService;
    }
    public function deleteDatsan($id,$san,$datsan){
        DB::beginTransaction();
        try {
            DatSan::find($id)->delete();
            $time = substr($datsan->start_time, 0, 10) . " 00:00:00";
            $doanhthu = DoanhThu::where('idquan', $san->idquan)->where('time', $time)->first();
            if ($datsan->xacnhan == 1 && $doanhthu) {
                $tien = (int)$doanhthu->doanhthu - (int)$datsan->price;
                date_default_timezone_set("Asia/Ho_Chi_Minh");
                $time1 = date('Y-m-d H:i:s');
                DB::update('update doanhthus set doanhthu= ?,updated_at=? where id = ?', [$tien, $time1, $doanhthu->id]);
            }
                
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            //return false;
            throw new \Exception($e->getMessage());
        }
       
    }
    public function thư(){
        DB::beginTransaction();
        try {
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
       
    }
    public function find($id){
        return DatSan::find($id);
    }
    public function getDatSanById($id,$xacnhan){
        return DatSan::where('id',$id)->where('xacnhan',$xacnhan)->first();
    }
    public function xacNhanDatsan($datsan,$xacnhan,$start_time,$price,$san){
        DB::beginTransaction();
        try {
            if ($datsan->xacnhan==$xacnhan) {
                return "bạn không thể xác nhận được nữa";
            }
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d H:i:s');
            if ($time>$datsan->start_time) {
                return "bạn không thể xác nhận khi thời gian hiện tại lớn hơn thời gian đặt sân được";
            }
            DB::update('update datsans set xacnhan = ? where id = ?', [$xacnhan, $datsan->id]);
            $nam = substr($start_time, 0, 4);
            $thang = substr($start_time, 5, 2);
            $ngay = substr($start_time, 8, 2);            
            if ($xacnhan) {
                $doanhthu = DB::table('doanhthus')->whereDay('time', $ngay)->whereMonth('time', $thang)->whereYear('time', $nam)->where('idquan', '=', $san->idquan)->first();
                if ($doanhthu) {
                    $priceNew = (int)$doanhthu->doanhthu + (int)$price;
                    DB::update('update doanhthus set doanhthu=? ,updated_at=? where id = ?', [$priceNew, $time, $doanhthu->id]);    
                }else{
                    $data=[
                        "idquan"=>$san->idquan,
                        "doanhthu"=>$price,
                        "time"=> substr($start_time, 0, 10)." 00:00:00",
                        "created_at" => $time,
                        "updated_at" => $time
                    ];
                    DoanhThu::insert($data);
                }
                $chonquan = DB::table('chonquans')->where("iduser", $datsan->iduser)->where("idquan", $san->idquan)->first();
                if ($chonquan) {
                    DB::update('update chonquans set solan = ? where id = ?', [$chonquan->solan + 1, $chonquan->id]);
                } else {
                    DB::insert('insert into chonquans (iduser, idquan,solan) values (?, ?,?)', [$datsan->iduser, $san->idquan, 1]);
                }
            } else {
                $doanhthu = DB::table('doanhthus')->whereDay('time', $ngay)->whereMonth('time', $thang)->whereYear('time', $nam)->where('idquan', '=', $san->idquan)->first();
                if ($doanhthu) {
                    $priceNew = (int)$doanhthu->doanhthu - (int)$price;
                    DB::update('update doanhthus set doanhthu=? ,updated_at=? where id = ?', [$priceNew, $time, $doanhthu->id]);
                }
                $chonquan = DB::table('chonquans')->where("iduser", $datsan->iduser)->where("idquan", $san->idquan)->first();
                if ($chonquan) {
                    DB::update('update chonquans set solan = ? where id = ?', [$chonquan->solan - 1, $chonquan->id]);
                } else {
                    DB::insert('insert into chonquans (iduser, idquan,solan) values (?, ?,?)', [$datsan->iduser, $san->idquan, 1]);
                }
            }
            
            DB::commit();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
                
    }
    public function getListDatSanByIduser1($datsans)
    {
        $mangdatsantruocngayhientai=[];
        for ($i=0; $i < count($datsans); $i++) { 
            $san=$this->sanService->findById($datsans[$i]->idsan);
            $quan=$this->quanService->findById($san->idquan);
            $datsan = new datsanS(
                $datsans[$i]->id,
                $quan->name,
                $quan->address,
                $quan->phone,
                $san->name,
                $datsans[$i]->start_time,
                $san->numberpeople,
                $datsans[$i]->price,
                $datsans[$i]->xacnhan,
                $san->trangthai
            );
            array_push($mangdatsantruocngayhientai, $datsan);
        }
        return $mangdatsantruocngayhientai;
    }
    public function getListDatSanByIduser($iduser,$soluong)
    {
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $time = date('Y-m-d H:i:s');
        return DB::table('datsans')
                ->where('iduser', $iduser)
                ->where('start_time', '>=', $time)
                ->orderBy('start_time', 'asc')
                ->paginate($soluong);
        
        }
    public function getDatSansByInnkeeperAndIdquanAndNgay($sans,  $start_time){
        $datsans = array();
        $nam = substr($start_time, 0, 4);
        $thang = substr($start_time, 5, 2);
        $ngay = substr($start_time, 8, 2);
        foreach ($sans as $san) {
            $datsan = DB::table('datsans')->where('idsan', $san->id)->whereDay('start_time', $ngay)->whereMonth('start_time', $thang)->whereYear('start_time', $nam)->get();
            $datsannews = $this->mangdatsancuamotsan($datsan);
            array_push($datsans, $datsannews);
        }
        return $datsans;
    }
    public function mangdatsancuamotsan($datsans){
        $array = [false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false];
        for($i=0; $i<count($datsans); $i++){
            if (!$datsans[$i]->xacnhan) {
                break;
            }
            switch (substr($datsans[$i]->start_time,11,2)) {
                case "05":
                     $array[0] = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan,$this->userService->findById($datsans[$i]->iduser) , $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                     break;
                case "06":
                    $array[1] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "07":
                    $array[2] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "08":
                    $array[3] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "09":
                    $array[4] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "10":
                    $array[5] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "11":
                    $array[6] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "12":
                    $array[7]
                    = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "13":
                    $array[8]
                    = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "14":
                    $array[9]
                    = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "15":
                    $array[10] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "16":
                    $array[11]
                    = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "17":
                    $array[12] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "18":
                    $array[13]
                    = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "19":
                    $array[14]
                    = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "20":
                    $array[15] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->findById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                
                default:
                    break;
            }
        }
        return $array;
    }
    public function mangTinhTrangdatsancuamotsan($datdsan)
    {
        $array = [false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false];
        for ($i = 0; $i < count($datdsan); $i++) {
            switch (substr($datdsan[$i]->start_time, 11, 2)) {
                case "05":
                    $array[0] =true;
                    break;
                case "06":
                    $array[1] = true;
                    break;
                case "07":
                    $array[2] = true;
                    break;
                case "08":
                    $array[3] = true;
                    break;
                case "09":
                    $array[4] = true;
                    break;
                case "10":
                    $array[5] = true;
                    break;
                case "11":
                    $array[6] = true;
                    break;
                case "12":
                    $array[7] = true;
                    break;
                case "13":
                    $array[8] = true;
                    break;
                case "14":
                    $array[9] = true;
                    break;
                case "15":
                    $array[10] = true;
                    break;
                case "16":
                    $array[11] = true;
                    break;
                case "17":
                    $array[12] = true;
                    break;
                case "18":
                    $array[13] = true;
                    break;
                case "19":
                    $array[14] = true;
                    break;
                case "20":
                    $array[15] = true;
                    break;

                default:
                    break;
            }
        }
        return $array;
    }

    public function getTinhTrangDatSansByIdquanVaNgay($sans,$start_time)
    {
        $datsans = array();
        $nam = substr($start_time, 0, 4);
        $thang = substr($start_time, 5, 2);
        $ngay = substr($start_time, 8, 2);
        foreach ($sans as $san) {
            $datsan = DB::table('datsans')->where('idsan', $san->id)->whereDay('start_time', $ngay)->whereMonth('start_time', $thang)->whereYear('start_time', $nam)->get();
            $TRdatsan=$this->mangTinhTrangdatsancuamotsan($datsan);
            array_push($datsans, $TRdatsan);
        }
        return $datsans;
    }
    public function  addDatSan($request,$iduser){
        $datsan = DatSan::where('idsan', $request->get('idsan'))->where('start_time', $request->get('start_time'))->first();
        if ($datsan) {
            return  "Giờ này đã có người đặt rồi";
        }
        DB::beginTransaction();
        try {
           // return DB::table('datsans')->where('id',46)->first();
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d H:i:s');
            $data=[
                "idsan" =>$request->get('idsan'),
                "iduser" =>$iduser,
                "start_time" => $request->get('start_time'),
                "price"=>$request->get('price'),
                "xacnhan" =>false,
                "Create_time"=> $time
            ];
            
            Datsan::insert($data);
           
            DB::commit();
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return false;
    }
    public function thayDoiDatSanByInnkeeper($timeOld, $timeNew, $sanOld, $sanNew,$datsanOld){
        try {
            DB::beginTransaction();
            DB::update('update datsans set idsan = ?,start_time=?,price=? where id = ?', [$sanNew->id, $timeNew,$sanNew->priceperhour, $datsanOld->id]);
            
            $nam = substr($timeOld, 0, 4);
            $thang = substr($timeOld, 5, 2);
            $ngay = substr($timeOld, 8, 2);
            $doanhthuOld = DB::table('doanhthus')->whereDay('time', $ngay)->whereMonth('time', $thang)->whereYear('time', $nam)->where('idquan', '=', $sanOld->idquan)->first();
            if (!$doanhthuOld) {
                DB::insert('insert into doanhthus (idquan, doanhthu ,time) values (?, ?,?)', [$sanOld->idquan, 0,$nam."-".$thang."-".$ngay."00:00:00"]);
                $doanhthuOld = DB::table('doanhthus')->whereDay('time', $ngay)->whereMonth('time', $thang)->whereYear('time', $nam)->where('idquan', '=', $sanOld->idquan)->first();
            }
            $priceOld=$doanhthuOld->doanhthu-$sanOld->priceperhour;
            DB::update('update doanhthus set doanhthu = ? where id = ?', [$priceOld, $doanhthuOld->id]);

            $nam = substr($timeNew, 0, 4);
            $thang = substr($timeNew, 5, 2);
            $ngay = substr($timeNew, 8, 2);
            $doanhthuNew = DB::table('doanhthus')->whereDay('time', $ngay)->whereMonth('time', $thang)->whereYear('time', $nam)->where('idquan', '=', $sanNew->idquan)->first();
            if (!$doanhthuOld) {
                DB::insert('insert into doanhthus (idquan, doanhthu ,time) values (?, ?,?)', [$sanNew->idquan, 0, $nam . "-" . $thang . "-" . $ngay . "00:00:00"]);
                $doanhthuNew = DB::table('doanhthus')->whereDay('time', $ngay)->whereMonth('time', $thang)->whereYear('time', $nam)->where('idquan', '=', $sanNew->idquan)->first();
            }
            $priceNew = $doanhthuNew->doanhthu + $sanNew->priceperhour;
            DB::update('update doanhthus set doanhthu = ? where id = ?', [$priceNew, $doanhthuNew->id]);
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
        return false;
    }
    public function getdatsan($idsan,$start_time){
        return DatSan::where('idsan',$idsan)->where('start_time',$start_time)->first();
    }
    public function getAllDatSanByIdquan($idquan,$xacnhan,$time,$dau,$soluong){
        $sans=$this->sanService->getSansByIdquan($idquan);
        $nam= substr($time, 0, 4);
        $thang= substr($time,5, 2);
        $ngay = substr($time,8, 2);
        $datsans=[];
        $a=[];
        for ($i=0; $i < count($sans); $i++) {
            array_push($a, $sans[$i]->id);
        }
        if ($dau == "=") {
            $datsans = DatSan::where('xacnhan', $xacnhan)
                        ->whereYear("start_time", $dau, $nam)
                        ->whereMonth("start_time", $dau, $thang)
                        ->whereDay("start_time", $dau, $ngay)
                        ->whereIn("idsan", $a)
                        ->orderBy('start_time', 'asc')
                        ->paginate($soluong);
        } else {
            $datsans = DatSan::where('xacnhan', $xacnhan)
                        ->where("start_time", $dau, $time)
                        ->whereIn("idsan", $a)
                        ->orderBy('start_time', 'asc')
                        ->paginate($soluong);
        }
        
        return $datsans;
        
    }

    public function getAllDatSanByIdquan1($datsans)
    {
        $datsansnew=[];
        foreach ($datsans as $datsan) {
            $user = $this->userService->findById($datsan->iduser);
            $san = $this->sanService->findById($datsan->idsan);
            $ds = new Datsan2($datsan->id, $san, $user, $datsan->start_time, $datsan->price, $datsan->xacnhan);
            array_push($datsansnew, $ds);
        }
        return $datsansnew;
    }
    public function getListDatSanByInnkeeper($innkeeper,$start_time){
        $quans=$this->quanService->getQuanByPhoneDaduocduyet( $innkeeper->phone);
        $datsans = array();
        
        foreach ($quans as $quan) {
            $sans= $this->sanService->getSansByIdquanVaTrangthai($quan->id,1);
            $datsancuaquan=new datsancuaquan($quan->id,$quan->name,$quan->address,$quan->phone,$sans,$this->getTinhTrangDatSansByIdquanVaNgay($sans, $start_time));
            array_push($datsans,$datsancuaquan);           
        }
        return $datsans; 
    }
    public function getDatSansCua1NgayByIdquanChodoanhThu($idquan, $xacnhan, $time)
    {
        $sans = $this->sanService->getSansByIdquan($idquan);
        $nam = substr($time, 0, 4);
        $thang = substr($time, 5, 2);
        $ngay = substr($time, 8, 2);
        $datsansnew = [];
        foreach ($sans as $san) {
            $datsans = DatSan::where('idsan', $san->id)->where('xacnhan', $xacnhan)->whereYear("start_time", "=", $nam)->whereMonth("start_time", "=", $thang)->whereDay("start_time", "=", $ngay)->get();
            foreach ($datsans as $datsan) {
                $user = $this->userService->findById($datsan->iduser);
                $ds = new Datsan2($datsan->id, $san, $user, $datsan->start_time, $datsan->price, $datsan->xacnhan);
                array_push($datsansnew, $ds);
            }
        }
        $keys = array_column($datsansnew, 'start_time');
        array_multisort($keys, SORT_ASC, $datsansnew);
        return $datsansnew;
    }
    
}
class Datsan1
{
    public $id;
    public $idsan;
    public $iduser;
    public $start_time;
    public $price;
    public $Create_time;

    public function __construct($id, $idsan, $iduser, $start_time, $price, $Create_time)
    {
        $this->id = $id;
        $this->name = $idsan;
        $this->iduser = $iduser;
        $this->start_time = $start_time;
        $this->price = $price;
        $this->Create_time = $Create_time;

    }
}
class DatsanByInnkeeper
{
    public $id;
    public $idsan;
    public $user;
    public $start_time;
    public $price;
    public $Create_time;

    public function __construct($id, $idsan, $user, $start_time, $price, $Create_time)
    {
        $this->id = $id;
        $this->idsan = $idsan;
        $this->user = $user;
        $this->start_time = $start_time;
        $this->price = $price;
        $this->Create_time = $Create_time;
    }
}

class datsancuaquan
{
    public $id;
    public $name;
    public $address;
    public $phone;
    public $sans;
    public $datsans;

    
    public function __construct($id, $name, $address, $phone,$sans,$datsans){
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->phone = $phone;
        $this->sans = $sans;
        $this->datsans = $datsans;
    }   
}
class datsanS
{
    public $id;
    public $nameQuan;
    public $addressQuan;
    public $phoneQuan;
    public $nameSan;
    public $time;
    public $numberpeople;
    public $price;
    public $xacnhan;
    public $trangthaisan;
    public function __construct($id, $nameQuan, $addressQuan, $phoneQuan,$nameSan,$time, $numberpeople,$price,$xacnhan,$trangthaisan){
        $this->id = $id;
        $this->nameQuan = $nameQuan;
        $this->addressQuan = $addressQuan;
        $this->phoneQuan = $phoneQuan;
        $this->nameSan = $nameSan;
        $this->time = $time;
        $this->numberpeople = $numberpeople;
        $this->price = $price;
        $this->xacnhan = $xacnhan;
        $this->trangthaisan= $trangthaisan;
    }   
}
class Datsan2
{
    public $id;
    public $san;
    public $user;
    public $start_time;
    public $price;
    public $xacnhan;
    public function __construct($id, $san,$user, $start_time, $price, $xacnhan)
    {
        $this->id = $id;
        $this->san = $san;
        $this->user = $user;
        $this->start_time = $start_time;
        $this->price = $price;
        $this->xacnhan = $xacnhan;
    }
}
