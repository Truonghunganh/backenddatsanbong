<?php

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
//use JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\Models\Review;
use App\Models\Models\Quan;

use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function reviewByUser($request,$iduser){
        DB::beginTransaction();
        try {
            $idquan = $request->get("idquan");
            $reviewNew = $request->get("review");
            $review = Review::where('iduser', $iduser)->where('idquan', $idquan)->first();
            if ($review) {
                $review->review=$reviewNew;
                $review->save();
            } else {
                $this->addReview($iduser, $idquan, $review);
            }
            $reviews = Review::where('idquan', $idquan)->get();
            $tong = 0;
            for ($i = 0; $i < count($reviews); $i++) {
                $tong += $reviews[$i]->review;
            }
            $quan =Quan::find($idquan);
            $quan->review = $tong / count($reviews);
            $quan->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }

                
    }
    public function findReviewByIduserVaIdquan($iduser,$idquan){
        return Review::where('iduser',$iduser)->where('idquan',$idquan)->first();
        
    }
    public function getAllReviewByIdquan($idquan){
        return Review::where('idquan',$idquan)->get();
    }
    public function addReview($iduser,$idquan,$review){
        DB::beginTransaction();
        try {
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-M-D H:I:S');
            $date = [
                    "iduser" => $iduser,
                    "idquan" => $idquan,
                    "review" => $review,
                    "Review_time" => $time
                ];
            Review::insert($date);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }

    }
    public function findById($id){
        return Review::find($id);
    }
}
