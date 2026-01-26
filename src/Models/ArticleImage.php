<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ArticleImage extends Model
{
    use HasFactory, HasUuids;

    /**
     * Signed URL expiry duration for private images (7 days in minutes).
     */
    private const SIGNED_URL_EXPIRY_MINUTES = 60 * 24 * 7;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'article_id',
        'original_url',
        'filename',
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
            'position' => 'integer',
            'is_public' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function getUrlAttribute(): string
    {
        $disk = Storage::disk(config('filesystems.default'));
        $diskDriver = config('filesystems.disks.' . config('filesystems.default') . '.driver');

        // For local filesystem, use direct storage URL
        // (local dev only - prod uses S3 with signed URLs)
        if ($diskDriver === 'local') {
            return url('/storage/' . $this->filename);
        }

        // Public images get direct storage URL
        if ($this->is_public) {
            return $disk->url($this->filename);
        }

        // Private images use signed URLs for cloud storage
        try {
            return $disk->temporaryUrl(
                $this->filename,
                now()->addMinutes(self::SIGNED_URL_EXPIRY_MINUTES)
            );
        } catch (\RuntimeException) {
            // Driver doesn't support temporary URLs
            // Return API endpoint URL for authenticated access
            return url("/api/article-images/{$this->id}");
        }
    }
}
