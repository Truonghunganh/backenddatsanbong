<?php

namespace App\Services;
use App\Models\Models\Comment;

use Illuminate\Support\Facades\DB;

use App\Services\UserService;
use App\Services\ReviewService;
class CommentService
{
    protected $reviewService;
    protected $userService;

    public function __construct(ReviewService $reviewService,UserService $userService){
        $this->reviewService = $reviewService;
        $this->userService = $userService;
    }

    public function getAllCommentCuaMotQuanByInnkeeper($idquan){
        $commentsnew = [];
        $reviews = $this->reviewService->getAllReviewByIdquan($idquan);
        for ($i = 0; $i < count($reviews); $i++) {
            $usernew = $this->userService->getUserById($reviews[$i]->iduser);
                
            $comments = Comment::where('idreview', $reviews[$i]->id)->get();
            for ($j = 0; $j < count($comments); $j++) {
                $comment = new Comment2($comments[$j]->id, $usernew->name, $reviews[$i]->review, $comments[$j]->Create_time, $comments[$j]->binhluan);
                array_push($commentsnew, $comment);
            }
        }
        for ($i = 0; $i < count($commentsnew) - 1; $i++) {
            for ($j = $i + 1; $j < count($commentsnew); $j++) {
                if ($commentsnew[$i]->Create_time < $commentsnew[$j]->Create_time) {
                    $a = $commentsnew[$i];
                    $commentsnew[$i] = $commentsnew[$j];
                    $commentsnew[$j] = $a;
                }
            }
        }
        return $commentsnew;
        
        
    }
    public function getAllCommentsCuaMotQuan($idquan,$user){
        $commentsnew=[];
        $usernew=$user;
        $quyenUpdate=1;
        $reviews=$this->reviewService->getAllReviewByIdquan($idquan);
        for ($i=0; $i < count($reviews); $i++) { 
            if ($user->id != $reviews[$i]->iduser) {
                $usernew = $this->userService->getUserById($reviews[$i]->iduser);
                $quyenUpdate=0;
            }
            $comments = Comment::where('idreview', $reviews[$i]->id)->get();
            for ($j=0; $j <count($comments); $j++) {
                $comment = new Comment1($comments[$j]->id,$usernew->name, $reviews[$i]->review, $comments[$j]->Create_time, $comments[$j]->binhluan, $quyenUpdate);
                array_push($commentsnew, $comment);
            }
            $quyenUpdate=1;
            $usernew=$user;
        }
        for ($i=0; $i < count($commentsnew)-1; $i++) { 
            for ($j=$i+1; $j <count($commentsnew); $j++) { 
                if ($commentsnew[$i]->Create_time < $commentsnew[$j]->Create_time) {
                    $a= $commentsnew[$i];
                    $commentsnew[$i]= $commentsnew[$j];
                    $commentsnew[$j]=$a;
                }
            }
        }
        return $commentsnew;
        
    }

    public function addComment($idquan, $user,$binhluan){
        DB::beginTransaction();
        try {
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            $time = date('Y-m-d H:i:s');
            $iduser = $user->id;
            $review = $this->reviewService->findReviewByIduserVaIdquan($iduser, $idquan);
            if (!$review) {
                $this->reviewService->addReview($iduser, $idquan, 0);
                $review = $this->reviewService->findReviewByIduserVaIdquan($iduser, $idquan);
            }
            DB::insert('insert into comments (idreview, binhluan,Create_time) values (?, ?,?)', [$review->id, $binhluan, $time]);
            DB::commit();
            return $this->getAllCommentsCuaMotQuan($idquan, $user);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        
    }
    public function findById($id){
        return Comment::find($id);
    }
    public function updateComment($id,$binhluan,$idquan,$user){
        DB::beginTransaction();
        try {
            DB::update('update comments set binhluan = ? where id = ?', [$binhluan, $id]);
            DB::commit();
            return $this->getAllCommentsCuaMotQuan($idquan, $user);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
    public function deleteComment($id,$idquan,$user){
        DB::beginTransaction();
        try {
            Comment::find($id)->delete();
            DB::commit();
            return $this->getAllCommentsCuaMotQuan($idquan, $user);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
       
    }
}

class Comment1{
    public $id;
    public $tenUser;
    public $review;
    public $Create_time;
    public $binhluan;
    public $quyenUpdate;
    public function __construct($id,$tenUser, $review, $Create_time, $binhluan, $quyenUpdate){
        $this->id = $id;
        $this->tenUser = $tenUser;
        $this->review = $review;
        $this->Create_time = $Create_time;
        $this->binhluan = $binhluan;
        $this->quyenUpdate = $quyenUpdate;
    }
}
class Comment2
{
    public $id;
    public $tenUser;
    public $review;
    public $Create_time;
    public $binhluan;
    public function __construct($id, $tenUser, $review, $Create_time, $binhluan)
    {
        $this->id = $id;
        $this->tenUser = $tenUser;
        $this->review = $review;
        $this->Create_time = $Create_time;
        $this->binhluan = $binhluan;
    }
}