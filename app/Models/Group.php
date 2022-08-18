<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "groups";
    protected $fillable = ['group_name', "members_id","leader_id"];
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
    public function ticket() {
        return $this->hasMany(Ticket::class,"group_id","id");
    }
    public function user() {
        return $this->belongsTo(User::class,"leader_id","id");
    }
}
