<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;

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
        public function setDeadlineAttribute($value)
        {
            $this->attributes['deadline'] = Carbon::parse($value)->format("Y-m-d H:i");
        }
        public function setAssigneesIdAttribute($value)
        {
            $this->attributes['assignees_id'] = is_null($value) || empty($value) ? NULL : json_encode($value);
        }
        public function setCcAttribute($value)
        {
            $this->attributes['cc'] = !is_null($value) ? $value : NULL;
        }
        // protected function deadline(): Attribute laravel 9
        // {
        //     return new Attribute(
        //         get: fn ($value) => Carbon::parse($value)->format("d-m-Y H:i"),
        //         set: fn ($value) => Carbon::parse($value)->format("Y-m-d H:i")
        //     );
        // }
        public function scopeOwnTicket($query)
        {
            return $query->where(function ($query) {
                 $query->where("creator_id", Auth::id())
                       ->orWhere("cc",Auth::id());
            });
        }
        public function scopeConditionLevel($query, $request) {
             $query->when($request->has("level") && $request->level != "", function ($query) use($request) {
                return $query->where("level", $request->level);
            });
            return $query;
        }
        public function scopeConditionStatus($query, $request) {
            $query->when($request->has("status") && $request->status != "", function ($query) use($request) {
                if($request->status == 5)
                return $query->whereIn("status", [3,4]);
                return $query->where("status", $request->status);
            });
            return $query;
        }
        public function scopeConditionTime($query, $request) {
            $query->when($request->has("time") && $request->time != "", function ($query) use($request) {
                if($request->time == "today") {
                    return $query->whereDate('created_at', Carbon::today());
                }
                else if($request->time == "week") {
                    return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                } else if($request->time == "month")
                {
                    return $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                }
            });
            return $query;
        }
        public function scopeConditionGroup($query, $request) {
          $query->when($request->has("group_id") && $request->group_id != "", function ($query) use($request) {
                return $query->where("group_id", $request->group_id);
            });
            return $query;
        }


}
