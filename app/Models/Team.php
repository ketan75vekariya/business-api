<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'employeeName',
        'employeeDescription',
        'image_path',
    ];
}
