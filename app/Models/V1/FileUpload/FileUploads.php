<?php

namespace App\Models\V1\FileUpload;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUploads extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_name',
        'size',
        'duration',
        'extension',
        'mime_type',
        'url',
        'upload_type',
        'is_active',
        'media_id'
    ];
}
