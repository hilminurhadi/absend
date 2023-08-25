<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpDevice extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'emp_device';
    protected $guarded = [];
    public $timestamps = false;
}
