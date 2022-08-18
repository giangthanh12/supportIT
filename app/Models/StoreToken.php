<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreToken extends Model
{
    use HasFactory;
    protected $table = "store_tokens";
    protected $fillable = [
        "user_id",
        "access_token",
        "domain",
        "refresh_token",
        "application_token",
    ];

}
