<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Models\DoanhThu;
use App\Models\Models\DatSan;

use Illuminate\Database\Seeder;

class DatSanTableSeeder extends Seeder
{
    public function run()
    {
        $users=DB::table('users')->where("role","user")->get();
        $gios=["05:00:00", "06:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00"];
        for ($nam = 2021; $nam < 2022; $nam++) {
            for ($thang = 1; $thang < 13; $thang++) {
                switch ($thang) {
                    case 1:
                        $gios = [["05:00:00","06:00:00", "07:00:00", "08:00:00"], ["15:00:00", "16:00:00","17:00:00", "18:00:00", "19:00:00", "20:00:00"]];
                        break;
                    case 2:
                        $gios = [["05:00:00", "06:00:00", "17:00:00", "18:00:00", "19:00:00", "20:00:00"]];
                        break;
                    case 3:
                        $gios = [ ["07:00:00", "08:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00", "19:00:00", "20:00:00"]];
                        break;
                    case 4:
                        $gios = [["05:00:00", "06:00:00", "07:00:00"],["17:00:00", "18:00:00","19:00:00", "20:00:00"]];
                        break;
                    case 5:
                        $gios = [["06:00:00", "07:00:00", "15:00:00", "16:00:00", "17:00:00"]];
                        break;
                    case 6:
                        $gios = [["05:00:00", "06:00:00", "07:00:00"], ["15:00:00", "16:00:00", "17:00:00","19:00:00", "20:00:00"]];
                        break;
                    case 7:
                        $gios = [["15:00:00", "16:00:00", "17:00:00","19:00:00", "20:00:00"]];
                        break;
                    case 8:
                        $gios = [["05:00:00", "06:00:00","07:00:00", "15:00:00", "16:00:00","17:00:00"]];
                        break;
                    case 9:
                        $gios = [["05:00:00", "06:00:00", "07:00:00"], ["15:00:00", "16:00:00", "17:00:00"]];
                        break;
                    case 10:
                        $gios = [["05:00:00", "06:00:00", "07:00:00","08:00:00"]];
                        break;
                    case 11:
                        $gios = [["05:00:00", "06:00:00", "07:00:00"], ["15:00:00", "16:00:00", "17:00:00"]];
                        break;
                    case 12:
                        $gios = [["15:00:00", "16:00:00", "17:00:00", "18:00:00"]];
                        break;

                    default:
                        # code...
                        break;
                }
                for ($ngay = 1; $ngay < 28; $ngay++) {
                    $quans=DB::table('quans')->where("trangthai",true)->get();

                    for ($idquan = 0; $idquan <count($quans) ; $idquan++) {
                        $quan=$quans[$idquan];
                        $sans= DB::table('sans')->where('idquan', $quan->id)->get();
                        for ($i=0; $i <count($sans) ; $i++) { 
                            $k=mt_rand(0, count($gios));
                            for ($gio=0; $gio <$k ; $gio++) {
                                // đặt sân
                                $user= $users[mt_rand(0, count($users) - 1)];
                                $data = [
                                    "idsan" => $sans[$i]->id,
                                    "iduser" => $user->id,
                                    "start_time"=>$nam."-".$thang."-".$ngay." ".$gios[$gio][mt_rand(0, count($gios[$gio])-1)],
                                    "price"=>$sans[$i]->priceperhour,
                                    "xacnhan"=>true,
                                    "Create_time"=> Carbon::now()   

                                ];    
                                DatSan::insert($data);
                                // doanh thu
                                $doanhthuold = DoanhThu::where("idquan", $quan->id)->whereYear("time", $nam)->whereMonth("time", $thang)->whereDay('time', $ngay)->first();
                                if ($doanhthuold) {
                                    DB::update('update doanhthus set doanhthu = ? where id= ?', [(int)$doanhthuold->doanhthu+ (int)$sans[$i]->priceperhour,$doanhthuold->id]);
                                } else {
                                    $data = [
                                        "idquan" => $quan->id,
                                        "doanhthu" => $sans[$i]->priceperhour,
                                        "time" => $nam . "-" . $thang . "-" . $ngay . " 00:00:00"
                                    ];
                                    DoanhThu::insert($data);            
                                }
                                // chọn quán
                                $chonquan=DB::table('chonquans')->where("iduser",$user->id)->where("idquan", $quan->id)->first();
                                if ($chonquan) {
                                    DB::update('update chonquans set solan = ? where id = ?', [$chonquan->solan+1, $chonquan->id]);
                                } else {
                                    DB::insert('insert into chonquans (iduser, idquan,solan) values (?, ?,?)', [$user->id, $quan->id,1]);
                                }
                                
                            }
                            
                        }
                        
                    }
                }
            }

        }         
    }
}
