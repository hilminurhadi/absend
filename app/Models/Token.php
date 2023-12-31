<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'token';
    protected $guarded = [];
    public $timestamps = false;
}
