<?php

namespace App\Models\V1\Media;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\V1\FileUpload\FileUploads;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Medias extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_title',
        'media_description',
        'user_calendar_premiere_id',
        'is_active',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function file(): HasMany
    {
        return $this->hasMany(FileUploads::class);
    }

}



