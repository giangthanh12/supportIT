<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;
    protected $table = "holidays";
    protected $fillable = ['date', "title"];
    public $timestamps = false;
    public function getDateFormatAttribute($value) {
        return Carbon::parse($value)->format("d-m-Y");
    }
}
