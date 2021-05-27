<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\Models\San;

class SanService
{
    public function getSansByIdquanVaTrangthai($idquan,$trangthai)
    {
        $sans = San::query()->where('idquan', '=', $idquan)->where('trangthai',$trangthai)->get();
        $sansnew = [];
        for ($i = 0; $i < $sans->count(); $i++) {
            array_push($sansnew, new San1($sans[$i]->id, $sans[$i]->idquan, $sans[$i]->name, $sans[$i]->numberpeople, $sans[$i]->trangthai, $sans[$i]->priceperhour, $sans[$i]->Create_time, $sans[$i]->xacnhan));
        }
        $keys = array_column($sansnew, 'id');
        // SORT_ASC : laf tăng dần
        array_multisort($keys, SORT_ASC, $sansnew);

        return $sansnew;
    }
    
    public function getSansByIdquan($idquan)
    {
          $sans =San::query()->where('idquan', '=', $idquan)->get();
          $sansnew=[];
          for ($i=0; $i <$sans->count(); $i++) {
            array_push($sansnew,new San1($sans[$i]->id,$sans[$i]->idquan,$sans[$i]->name,$sans[$i]->numberpeople,$sans[$i]->trangthai,$sans[$i]->priceperhour,$sans[$i]->Create_time,$sans[$i]->xacnhan));  
          }
        $keys = array_column($sansnew, 'id');
        // SORT_ASC : laf tăng dần
        array_multisort($keys, SORT_ASC, $sansnew);
       
        return $sansnew;
        
    }
    public function findById($id)
    {
        return San::find($id);
    }
   
    public function addSanByInnkeeper($request){
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $time = date('Y-m-d H:i:s');
         
        return DB::insert(
            'insert into sans (idquan,name,numberpeople,trangthai,priceperhour,Create_time) values (?,?, ?,?, ?,?)',
            [
                $request->get('idquan'),
                $request->get('name'),
                $request->get('numberpeople'),
                0,
                $request->get('priceperhour'),
                $time

            ]
        );
        
    }
    public function thayDoiTrangthaiSanByInnkeeper($idsan,$trangthai){
        try {
            DB::beginTransaction();
            DB::update('update sans set trangthai = ? where id = ?', [!$trangthai,$idsan]);
            
            DB::commit();
            return true;
            //code...
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        
        }        
    }
    public function editSanByInnkeeper($request)
    {
        return DB::update(
            'update sans set name = ?, numberpeople = ?, priceperhour =? where id=?',
            [
                $request->get('name'),
                $request->get('numberpeople'),
                $request->get('priceperhour'),
                $request->get('id'),
            ]
        );
    }
    
}
class San1{
    public $id;
    public $idquan;
    public  $name;
    public $numberpeople;
    public $trangthai;
    public $priceperhour;
    public $Create_time;
    public $xacnhan;
    public function __construct($id,$idquan ,$name, $numberpeople,$trangthai ,$priceperhour,$Create_time,$xacnhan){
        $this->id = $id;
        $this->idquan = $idquan;
        $this->name = $name;
        $this->numberpeople = $numberpeople;
        $this->trangthai = $trangthai;
        $this->priceperhour = $priceperhour;
        $this->Create_time = $Create_time;
        $this->xacnhan = $xacnhan;
    }
}