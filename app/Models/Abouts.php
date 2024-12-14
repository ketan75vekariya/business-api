<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abouts extends Model
{
    protected $fillable = [
        'aboutTitle',
        'aboutDescription',
        'whyUs',
        'goal',
        'mission',
    ];
}
