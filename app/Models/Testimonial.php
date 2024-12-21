<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'clientName',
        'clientReview',
        'image_path'
    ];
}
