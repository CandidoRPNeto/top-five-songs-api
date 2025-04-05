<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'user_id',
        'admin_id',
        'approved',
        'link'
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'admin_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
