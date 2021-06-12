<?php

namespace App\Services;

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
                DB::update('update doanhthus set doanhthu= ? where id = ?', [$tien, $doanhthu->id]);
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
            $xacnhan = DB::update('update datsans set xacnhan = ? where id = ?', [$xacnhan, $datsan->id]);
            $nam = substr($start_time, 0, 4);
            $thang = substr($start_time, 5, 2);
            $ngay = substr($start_time, 8, 2);
            $doanhthu = DB::table('doanhthus')->whereDay('time', $ngay)->whereMonth('time', $thang)->whereYear('time', $nam)->where('idquan', '=', $san->idquan)->first();
            $priceNew = (int)$doanhthu->doanhthu + (int)$price;
            DB::update('update doanhthus set doanhthu=? where id = ?', [$priceNew, $doanhthu->id]);
            $chonquan = DB::table('chonquans')->where("iduser", $datsan->iduser)->where("idquan", $san->idquan)->first();
            if ($chonquan) {
                DB::update('update chonquans set solan = ? where id = ?', [$chonquan->solan + 1, $chonquan->id]);
            } else {
                DB::insert('insert into chonquans (iduser, idquan,solan) values (?, ?,?)', [$datsan->iduser, $san->idquan, 1]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
            throw new \Exception($e->getMessage());
        }
                
    }
    public function getListDatSanByIduser($iduser)
    {
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $time = date('Y-m-d H:i:s');
        // return DatSan::orderBy('start_time', 'asc')
        // ->where('iduser', $iduser)->where('start_time', '>=', $time)->join("sans","datsans.idsan",'sans.id')->join("quans","sans.idquan","quans.id")->get();
        
        // return DatSan::select('id', 'start_time', "price", "xacnhan")->orderBy('start_time', 'desc')
        //     ->where('iduser', $iduser)->where('start_time', '>=', $time)->where(function ($query) {

        //     San::select("trangthai")->where("id","=", $query->idsan)->first()->where(function ($query) {
        //         Quan::select("name", 'phone', 'address')->where('id','=', $query->idquan);
        //     });
        // })->get();
        
        $listdatsanByiduser= DB::table('datsans')->where('iduser', $iduser)->where('start_time','>=', $time)->get();
        $sans= DB::table('sans')->get();
        $quans=DB::table('quans')->get();
        $san=$sans[0];
        $quan=$quans[0];
        $mangdatsantruocngayhientai=[];
        for ($i=0; $i < count($listdatsanByiduser); $i++) { 
            for ($j=0; $j <count($sans) ; $j++) { 
                if ($listdatsanByiduser[$i]->idsan==$sans[$j]->id) {
                    $san=$sans[$j];
                    for ($k = 0; $k < count($quans); $k++) {
                        if ($quans[$k]->id == $sans[$j]->idquan) {
                            $quan=$quans[$k];
                            break;
                        }
                    }
                    break;
                }
            }
            $datsan=new datsanS($listdatsanByiduser[$i]->id,
                    $quan->name,
                    $quan->address,
                    $quan->phone,
                    $san->name,
                    $listdatsanByiduser[$i]->start_time,
                    $san->numberpeople,
                    $listdatsanByiduser[$i]->price,
                    $listdatsanByiduser[$i]->xacnhan
                    ,$san->trangthai);
            array_push($mangdatsantruocngayhientai,$datsan);
        }
        $keys = array_column($mangdatsantruocngayhientai, 'time');
        // SORT_ASC : laf tăng dần
        array_multisort($keys, SORT_ASC,$mangdatsantruocngayhientai);
        return $mangdatsantruocngayhientai;
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
                     $array[0] = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan,$this->userService->getUserById($datsans[$i]->iduser) , $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                     break;
                case "06":
                    $array[1] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "07":
                    $array[2] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "08":
                    $array[3] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "09":
                    $array[4] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "10":
                    $array[5] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "11":
                    $array[6] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "12":
                    $array[7]
                    = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "13":
                    $array[8]
                    = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "14":
                    $array[9]
                    = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "15":
                    $array[10] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "16":
                    $array[11]
                    = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "17":
                    $array[12] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "18":
                    $array[13]
                    = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "19":
                    $array[14]
                    = new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
                    break;
                case "20":
                    $array[15] =
                    new DatsanByInnkeeper($datsans[$i]->id, $datsans[$i]->idsan, $this->userService->getUserById($datsans[$i]->iduser), $datsans[$i]->start_time, $datsans[$i]->price, $datsans[$i]->Create_time);
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
            return  78;
        }
        DB::beginTransaction();
        try {
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d H:i:s');
            
            DB::insert('insert into datsans (idsan, iduser,start_time,price,xacnhan,Create_time) values (?,?, ?,?, ?,?)',
             [$request->get('idsan'), $iduser,$request->get('start_time'),$request->get('price'),false,$time]);
            
            // Datsan::updateOrCreate(
            //     [
            //         'id' => null
            //     ],
            //     [
            //         'idsan' => $request->get('idsan'),
            //         'iduser' => $iduser,
            //         'start_time' => $request->get('start_time'),
            //         'price' => $request->get('price'),
            //         'xacnhan' => false,
            //         'Create_time' => $time
            //     ]
            // );
            DB::commit();
            return 9;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return false;
    }
    
                // $mangdatsantruoc1tuan = DB::table('datsans')->where('start_time', '<', substr($week, 0, 10))->get();
                // if (count($mangdatsantruoc1tuan) != 0) {
                //     $id = $mangdatsantruoc1tuan[0]->id;
                // }
                // if (count(DB::table('datsans')->where('start_time', '=', $request->get('start_time'))->where('idsan', '=', $idsan)->get()) == 0) {
                //     return Datsan::updateOrCreate(
                //         [
                //             'id' => $id
                //         ],
                //         [
                //             'idsan' => $request->get('idsan'),
                //             'iduser' => $iduser,
                //             'start_time' => $request->get('start_time'),
                //             'price' => $request->get('price'),
                //             'xacnhan'=>false,
                //             'Create_time' => Carbon::now()
                //         ]

                //     );
                // }
            
            
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

    public function getAllDatSanByIdquan($idquan,$xacnhan,$time,$dau){
        $sans=$this->sanService->getSansByIdquan($idquan);
        $nam= substr($time, 0, 4);
        $thang= substr($time,5, 2);
        $ngay = substr($time,8, 2);
        $datsansnew=[];
        foreach ($sans as $san) {
            if ($dau=="=") {
                $datsans = DatSan::where('idsan', $san->id)->where('xacnhan', $xacnhan)->whereYear("start_time", $dau, $nam)->whereMonth("start_time", $dau, $thang)->whereDay("start_time", $dau, $ngay)->get();
            } else {
                $datsans = DatSan::where('idsan', $san->id)->where('xacnhan', $xacnhan)->where("start_time", $dau, $time)->get();
                # code...
            }
            
            foreach ($datsans as $datsan) {
                $user=$this->userService->getUserById($datsan->iduser);
                $ds=new Datsan2($datsan->id,$san,$user,$datsan->start_time,$datsan->price,$datsan->xacnhan);
                array_push($datsansnew,$ds);
            }

        }
        $keys = array_column($datsansnew, 'start_time');
        array_multisort($keys, SORT_ASC, $datsansnew);
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
