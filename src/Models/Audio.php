<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Audio extends Model
{
    use HasFactory, HasUuids;

    /**
     * Signed URL expiry duration for private audio (7 days in minutes).
     */
    private const SIGNED_URL_EXPIRY_MINUTES = 60 * 24 * 7;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'audios';

    protected $fillable = [
        'user_id',
        'note_id',
        'filename',
        'original_name',
        'title',
        'artist',
        'mime_type',
        'size',
        'duration',
        'bitrate',
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
            'duration' => 'float',
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

        if ($this->is_public) {
            return $disk->url($this->filename);
        }

        // Return signed URL for private audio if driver supports it (S3, etc.)
        try {
            return $disk->temporaryUrl(
                $this->filename,
                now()->addMinutes(self::SIGNED_URL_EXPIRY_MINUTES)
            );
        } catch (\RuntimeException) {
            // Driver doesn't support temporary URLs (e.g., local driver)
            // Return API endpoint URL for authenticated access
            return url("/api/audios/{$this->id}");
        }
    }
}
