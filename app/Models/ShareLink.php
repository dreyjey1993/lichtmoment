<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareLink extends Model
{
    protected $fillable = ['project_id', 'token', 'password_hash', 'download_enabled', 'expires_at', 'access_count'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && strtotime($this->expires_at) < time();
    }
}
