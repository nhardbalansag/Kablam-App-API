<?php

namespace App\Models\V1\Media;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medias extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_title',
        'media_description',
        'is_active',
        'user_id'
    ];
}



