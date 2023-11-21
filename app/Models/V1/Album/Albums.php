<?php

namespace App\Models\V1\Album;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\V1\Media\Medias;
use App\Models\V1\MediaAlbum\MediaAlbums;

class Albums extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_album_title',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(MediaAlbums::class);
    }
}
