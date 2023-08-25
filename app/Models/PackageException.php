<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageException extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'package_exception';
    protected $guarded = [];
    public $timestamps = false;
}
