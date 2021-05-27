<?php

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
//use JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\Models\Review;

use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function reviewByUser($request,$iduser){
        $idquan = $request->get("idquan");
        $reviewNew=$request->get("review");
        $review=Review::where('iduser',$iduser)->where('idquan', $idquan)->first();
        if ($review) {
            DB::update('update reviews set review = ? where id = ?', [$reviewNew,$review->id]);
        }
        else {
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d h:i:s');
            DB::insert('insert into reviews (iduser, idquan,review,Review_time) values (?, ?,?,?)', [$iduser,$idquan,$reviewNew, $time]);
        }
        $reviews=Review::where('idquan',$idquan)->get();
        $tong = 0;
        for ($i=0; $i < count($reviews); $i++) { 
            $tong+=$reviews[$i]->review;
        }
        DB::update('update quans set review = ? where id = ?', [$tong/count($reviews),$idquan]);        
    }
    public function findReviewByIduserVaIdquan($iduser,$idquan){
        return Review::where('iduser',$iduser)->where('idquan',$idquan)->first();
        
    }
    public function getAllReviewByIdquan($idquan){
        return Review::where('idquan',$idquan)->get();
    }
    public function addReview($iduser,$idquan,$review){
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $time = date('Y-m-d h:i:s');
        DB::insert('insert into reviews (iduser, idquan,review,Review_time) values (?, ?,?,?)', [$iduser, $idquan, $review, $time]);
    }
    public function findById($id){
        return Review::find($id);
    }
}
