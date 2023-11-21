<?php

namespace App\Models\V1\MediaAlbum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\V1\Media\Medias;

class MediaAlbums extends Model
{
    use HasFactory;

    protected $fillable = [
        'albums_id',
        'medias_id',
        'users_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medias(): HasMany
    {
        return $this->hasMany(Medias::class);
    }
}
