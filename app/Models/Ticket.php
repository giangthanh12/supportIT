<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "tickets";
    protected $fillable = [
        'title',
        'email',
        'creator_id',
        'cc',
        'name_creator',
        'email_creator',
        'group_id',
        'assignees_id',
        'confirm_attend',
        'content',
        'file',
        'level',
        'status',
        'deadline',
        'confirm_deadline'
    ];
    public function getCreatedAtAttribute($value) {
        return Carbon::createFromTimestamp(strtotime($value))
        ->timezone(Config::get('app.timezone'))->format("M d, H:i");
        //remove this one if u want to return Carbon object
    }
    public function getTimeAgoAttribute($value) {
        return Carbon::parse($value)->diffForHumans();
    }
    public function user() {
        return $this->belongsTo(User::class,"creator_id","id");
    }
    public function group() {
        return $this->belongsTo(Group::class,"group_id","id");
    }
    public function getMembersAttribute($value)
        {
           $data = [];
            try {
             $value = json_decode($value);
             foreach($value as $val) {
                $user = User::select("id", "name", "avatar")->where("id",$val)->first();
                $data[] = $user;
             }
            } catch (\Throwable $th) {
                return false;
            }
            return $data;
        }
        public function getDeadlineAttribute($value)
        {
            return Carbon::parse($value)->format("d-m-Y H:i");
        }
}
