<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use App\Models\Models\Quan;

class QuanService
{
    public function deleteQuanByAdmin($id,$image){
        DB::beginTransaction();
        try {
            File::delete($image);
            Quan::find($id)->delete();      
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
       
       
    }
    public function searchListQuans($trangthai,$search){
        return DB::table("quans")->where('trangthai',"=",$trangthai)
        ->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
        })->get();
       
    }
    public function searchListQuans1($trangthai,$search)
    {
        $quans=Quan::where("trangthai",$trangthai);
        $mang = explode(" ", $search);
        for ($i=0; $i <count($mang) ; $i++) { 
            $a=$mang[$i];
            $quans->where( function ($query)use ($a) {
                $query->where('name', 'like', '%' . $a . '%')
                    ->orwhere('address', 'like', '%' . $a . '%')
                    ->orwhere('phone', 'like', '%' . $a . '%');
            });
        };
        return $quans->get();
    }

    public function getAllQuan(){
        return Quan::all();
    }
    public function findById($id)
    {
        return Quan::find($id);
    }
    public function findByIdVaTrangThai($id,$trangthai)
    {
        return Quan::where('id',$id)->where('trangthai',$trangthai)->first();
    }
    public function findQuanChuaduyetById($id)
    {
        return Quan::where('id', $id)->where('trangthai',0)->get();
    }
    public function getAllQuansByTrangthai($trangthai)
    {
        return Quan::where('trangthai', $trangthai)->get();
    }
    public function getListQuansByTrangthaiVaPage($trangthai,$soluong){
        return Quan::where('trangthai',$trangthai)->paginate($soluong);
    }
    public function suaSoDienThoai($phone,$phoneNew){
        DB::beginTransaction();
        try {
            DB::update('update quans set phone = ? where phone = ?', [$phoneNew,$phone]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        
    }
    public function getListQuansByTrangthai($trangthai,$iduser)
    {
        DB::beginTransaction();
        try {
            $quans = Quan::where('trangthai', $trangthai)->get();
            $quansnew = [];
            for ($i = 0; $i < count($quans); $i++) {
                $chonquan = DB::table('chonquans')->where("iduser", $iduser)->where('idquan', $quans[$i]->id)->first();
                $solan = 0;
                if ($chonquan) {
                    $solan = $chonquan->solan;
                }
                array_push($quansnew, new Quan1($quans[$i]->id, $quans[$i]->name, $quans[$i]->image, $quans[$i]->address, $quans[$i]->phone, $quans[$i]->linkaddress, $quans[$i]->vido, $quans[$i]->kinhdo, $quans[$i]->review, $solan));
            }
            $keys = array_column($quansnew, 'solan');
            array_multisort($keys, SORT_DESC, $quansnew);
            DB::commit();
            return $quansnew;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        

    }
    public function  UpdateTrangThaiQuanTokenAdmin($request){
        DB::beginTransaction();
        try {
            DB::update('update quans set trangthai = ? where id =? ', [$request->get('trangthai'), $request->get('idquan')]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
    public function getListQuansByTokenInnkeeper($innkeeper,$trangthai){
        return Quan::where('trangthai',$trangthai)->where('phone',$innkeeper->phone)->get();
    }
    public function addQuanByInnkeeper($request, $token){
        DB::beginTransaction();
        try {
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d H:i:s');
            $nameImage = $token->phone . "_" . str_replace(':', '_', $time) . "_" . $request->file('image')->getClientOriginalName();
            $file = $request->file('image');
            $file->move('image\Quan', $nameImage);
            DB::insert(
                'insert into quans (name,image,address,phone,linkaddress,trangthai,vido,kinhdo,review,Create_time) values (?,?, ?,?, ?,?,? ,?,?,?)',
                [
                    $request->get('name'),
                    "image/Quan/" . $nameImage,
                    $request->get('address'),
                    $token->phone,
                    $request->get('linkaddress'),
                    0,
                    $request->get('vido'),
                    $request->get('kinhdo'),
                    0,
                    $time
                ]
            );
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
    public function test(){
        File::delete('image/Quan/0987654321_2021-02-08_02_18_30.jpg');
    }
    public function editQuanByTokenInnkeeper($request, $getQuanById){
        DB::beginTransaction();
        try {
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d H:i:s');
            if ($request->hasFile('image')) {
                try {
                    File::delete($getQuanById->image); // xóa hình củ đi
                } catch (\Throwable $th) {
                }
                $nameImage = $getQuanById->phone . "_" . str_replace(':', '_', $time) . "_" . $request->file('image')->getClientOriginalName();
                $file = $request->file('image');
                $file->move('image\Quan', $nameImage); // thêm hình mới 
                DB::update(
                    'update quans set name=?,image=?,address=?,linkaddress=?,Create_time=?,kinhdo=?,vido=? where id = ?',
                    [
                        $request->get('name'),
                        "image/Quan/" . $nameImage,
                        $request->get('address'),
                        $request->get('linkaddress'),
                        $time,
                        $request->get('kinhdo'),
                        $request->get('vido'),
                        $request->get('id')
                    ]
                );
            } else {
                DB::update(
                    'update quans set name=?,address=?,linkaddress=?,Create_time=?,kinhdo=?,vido=? where id = ?',
                    [
                        $request->get('name'),
                        $request->get('address'),
                        $request->get('linkaddress'),
                        $time,
                        $request->get('kinhdo'),
                        $request->get('vido'),
                        $request->get('id')
                    ]
                );
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
    
    public function getQuanByPhone($phone){
        return Quan::where('phone',$phone)->get();
    }
    public function getQuanByPhoneDaduocduyet($phone)
    {
        return Quan::where('phone', $phone)->where('trangthai',1)->get();
    }
    public function getQuanByPhoneChuaduocduyet($phone)
    {
        return Quan::where('phone', $phone)->where('trangthai', 0)->get();
    }
    
    public function deleteQuanById($idquan)
    {
        DB::beginTransaction();
        try {
            $quan = Quan::find($idquan);
            if (File::delete($quan->image)) {
                Quan::find($idquan)->delete();
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

}

class Quan1
{
    public $id;
    public $name;
    public $image;
    public $address;
    public $phone;
    public $linkaddress;
    public $vido;
    public $kinhdo;
    public $review;
    public $solan;
    public function __construct($id, $name, $image, $address, $phone, $linkaddress,$vido,$kinhdo,$review,$solan)
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->address = $address;
        $this->phone= $phone;
        $this->linkaddress = $linkaddress;
        $this->vido = $vido;
        $this->kinhdo = $kinhdo;
        $this->review = $review;
        $this->solan = $solan;
    }
}
