<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class History extends Model
{
    use HasFactory;
    protected $table = "histories";
    protected $fillable = [
        'ticket_id',
        'creator_id',
        'desc_change',
    ];

    public function getCreatedAtAttribute($value) {
        return Carbon::createFromTimestamp(strtotime($value))
        ->timezone(Config::get('app.timezone'))->format("d-m-Y H:i:s");
        //remove this one if u want to return Carbon object
    }
    // public function getCreatedAtAttribute($value) {
    //     return Carbon::parse($value)->format("d-m-Y H:i:s");
    // }
    public function user() {
        return $this->belongsTo(User::class,"creator_id","id");
    }
    public function ticket() {
        return $this->belongsTo(Ticket::class,"ticket_id","id");
    }
}
