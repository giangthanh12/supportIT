<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;
    protected $primaryKey = 'cfg_key';
    public $timestamps = false;
    protected $fillable = [
        'cfg_key',
        'cfg_value'
    ];
}
