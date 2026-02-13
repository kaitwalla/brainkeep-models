<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory, HasUuids;

    /**
     * Signed URL expiry duration for private images (7 days in minutes).
     */
    private const SIGNED_URL_EXPIRY_MINUTES = 60 * 24 * 7;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'note_id',
        'filename',
        'original_name',
        'alt_text',
        'mime_type',
        'size',
        'width',
        'height',
        'position',
        'is_public',
    ];

    protected $attributes = [
        'is_public' => false,
    ];

    protected $appends = ['url'];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('brainkeep-models.user_model', 'App\\Models\\User'));
    }

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }

    public function getUrlAttribute(): string
    {
        $disk = Storage::disk(config('filesystems.default'));
        $diskDriver = config('filesystems.disks.' . config('filesystems.default') . '.driver');
        $assetsUrl = config('app.assets_url', config('app.url'));

        // For local filesystem, use assets URL for Cloudflare edge caching
        // (auth is handled at app level, not URL level)
        if ($diskDriver === 'local') {
            return rtrim($assetsUrl, '/') . '/storage/' . $this->filename;
        }

        if ($this->is_public) {
            return $disk->url($this->filename);
        }

        // Return signed URL for private images if driver supports it (S3, etc.)
        try {
            return $disk->temporaryUrl(
                $this->filename,
                now()->addMinutes(self::SIGNED_URL_EXPIRY_MINUTES)
            );
        } catch (\RuntimeException) {
            // Driver doesn't support temporary URLs
            // Return API endpoint URL via assets domain for authenticated access
            return rtrim($assetsUrl, '/') . "/api/images/{$this->id}";
        }
    }
}
