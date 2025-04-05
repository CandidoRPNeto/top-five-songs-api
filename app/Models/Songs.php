<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Songs extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'user_id',
        'title',
        'views',
        'youtube_id',
        'thumb',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
