<?php

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\Models\DoanhThu;
use App\Models\Models\Quan;

use Illuminate\Support\Facades\DB;

class DoanhThuService
{
    public function getDoanhthuByID($id){
        return DoanhThu::find($id);
    }
    public function getDoanhThuCuaAdminTheoNam($nam){
        $tongdoanhthus = [];
        $tong = 0;
        for ($i = 1; $i < 13; $i++) {
            $doanhthuold = DoanhThu::whereYear("time", $nam)->whereMonth("time", $i)->get();
            $tong = 0;
            for ($j = 0; $j < $doanhthuold->count(); $j++) {
                $tong += $doanhthuold[$j]->doanhthu;
            }
            $tong=$tong/100;
            array_push($tongdoanhthus, $tong);
        }
        return $tongdoanhthus;
    }
    public function getDoanhThuByInnkeeper($request){
        
        $idquan=$request->get("idquan");
        $nam = substr($request->get('time'), 0, 4);
        $thang = substr($request->get('time'), 5, 2);
        if (!is_int((int)$nam)||!is_int((int)$thang)) {
            return [];
        }
        $doanhthuold= DoanhThu::where("idquan",$idquan)->whereYear("time",$nam)->whereMonth("time",$thang)->get();
        $doanhthus=[];
        for ($i=0; $i < $doanhthuold->count(); $i++) { 
            array_push($doanhthus,new DoanhThus($doanhthuold[$i]->id,$doanhthuold[$i]->idquan,$doanhthuold[$i]->doanhthu,$doanhthuold[$i]->time));
        }
        $keys = array_column($doanhthus, 'time');
        // SORT_ASC : laf tăng dần
        array_multisort($keys, SORT_ASC, $doanhthus);
        return $doanhthus;
                
    }
    public function getDoanhThuTheoNamByInnkeeper($request)
    {
        $idquan = $request->get("idquan");
        $nam = substr($request->get('nam'), 0, 4);
        if (!is_int((int)$nam) ) {
            return [];
        }
        $tongdoanhthus = [];
        $tong=0;
        for ($i = 1; $i < 13; $i++) {
            $doanhthuold = DoanhThu::where("idquan", $idquan)->whereYear("time", $nam)->whereMonth("time", $i)->get();
            $tong=0;
            for ($j=0; $j <$doanhthuold->count(); $j++) { 
                $tong+=$doanhthuold[$j]->doanhthu;
            }
            array_push($tongdoanhthus,$tong );

        }
        return $tongdoanhthus;
    }
    public function TruDoanhThuCuaQuan($id,$tien){
        DB::beginTransaction();
        try {
            DB::update('update doanhthus set doanhthu= ? where id = ?', [$tien, $id]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
    public function getDoanhThuByIdquanAndTime($idquan,$time){
        return DoanhThu::where('idquan',$idquan)->where('time',$time)->first();
    }
    public function getDoanhThuListQuanByAdmin($request)
    {
        $nam = substr($request->get('time'), 0, 4);
        $thang = substr($request->get('time'), 5, 2);
        if (!is_int((int)$nam) || !is_int((int)$thang)) {
            return [];
        }
        $quans=Quan::where('trangthai',true)->get();
        $doanhthus = [];
        for ($i = 0; $i < $quans->count(); $i++) {
            $tien=0;
            $doanhthuold = DoanhThu::where("idquan", $quans[$i]->id)->whereYear("time", $nam)->whereMonth("time", $thang)->get();
            for ($j=0; $j <$doanhthuold->count(); $j++) { 
                $tien+=(int)$doanhthuold[$j]->doanhthu;
            }
            array_push($doanhthus, new DoanhThuQuan($quans[$i]->id,$quans[$i]->name,$quans[$i]->address,$quans[$i]->phone,$tien, $tien/100));

        }
        $keys = array_column($doanhthus, 'idquan');
        // SORT_ASC : laf tăng dần
        array_multisort($keys, SORT_ASC, $doanhthus);
        return $doanhthus;
    }
    public function getDoanhThuListQuanCuaMotNamByAdmin($nam)
    {
        $quans = Quan::where('trangthai', true)->get();
        $doanhthus = [];
        for ($i = 0; $i < $quans->count(); $i++) {
            $tien = 0;
            $doanhthuold = DoanhThu::where("idquan", $quans[$i]->id)->whereYear("time", $nam)->get();
            for ($j = 0; $j < $doanhthuold->count(); $j++) {
                $tien += (int)$doanhthuold[$j]->doanhthu;
            }
            array_push($doanhthus, new DoanhThuQuan($quans[$i]->id, $quans[$i]->name, $quans[$i]->address, $quans[$i]->phone, $tien, $tien / 100));
        }
        $keys = array_column($doanhthus, 'idquan');
        // SORT_ASC : laf tăng dần
        array_multisort($keys, SORT_ASC, $doanhthus);
        return $doanhthus;
    }
    
}

class DoanhThus
{
    public $id;
    public $idquan;
    public $doanhthu;
    public $time;
    public function __construct($id, $idquan, $doanhthu, $time)
    {
        $this->id = $id;
        $this->idquan = $idquan;
        $this->doanhthu = $doanhthu;
        $this->time = $time;
        
    }
}

class DoanhThuQuan
{
    public $idquan;
    public $namequan;
    public $addressquan;
    public $phonequan;
    public $doanhthuquan;
    public $doanhthuadmin;

    public function __construct($idquan, $namequan,$addressquan,$phonequan, $doanhthuquan,$doanhthuadmin)
    {
        $this->idquan = $idquan;
        $this->namequan = $namequan;
        $this->addressquan = $addressquan;
        $this->phonequan = $phonequan;
        $this->doanhthuquan = $doanhthuquan;
        $this->doanhthuadmin = $doanhthuadmin;
    }
}
