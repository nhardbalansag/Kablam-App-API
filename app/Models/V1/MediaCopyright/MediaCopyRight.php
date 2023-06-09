<?php

namespace App\Models\V1\MediaCopyright;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaCopyRight extends Model
{
    use HasFactory;

    protected $fillable = [
        'copyright_media_id',
        'copyright_owner_information',
        'user_id',
        'media_id',
        'status'
    ];
}
