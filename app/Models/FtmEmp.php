<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FtmEmp extends Model
{
    use HasFactory;

    protected $connection = 'mysql_ftm';
    protected $table = 'emp';
    protected $guarded = [];
    public $timestamps = false;
}
