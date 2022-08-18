<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    use HasFactory;
    protected $table = "comments";
    protected $fillable = ['sender_id', "ticket_id", "content"];
    public function user() {
        return $this->belongsTo(User::class, "sender_id", "id");
    }
    public function CheckUserLike($user_id) {
        $result = false;
        $data = DB::table("comment_user")->where("comment_id", $this->id)->where("user_id", $user_id)->first();
        if(!is_null($data))
        $result = true;
        return $result;
    }
    public function users() {
        return $this->belongsToMany(User::class,"comment_user","comment_id","user_id");
    }
    public function getTimeAgoAttribute($value) {
        return Carbon::parse($value)->diffForHumans();
    }
    public function getCheckLikeActiveAttribute() {
        $result = false;
        $data = DB::table("comment_user")->where("comment_id", $this->id)->where("user_id", Auth::id())->first();
        if(!is_null($data))
        $result = true;
        return $result;
    }
    protected $appends = ['check_like_active'];
}
