<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link extends Model
{
    use HasFactory, HasUuids;

    /**
     * Link type values.
     */
    public const TYPE_LINK = 'link';
    public const TYPE_VIDEO = 'video';

    /**
     * Image status values.
     */
    public const IMAGE_STATUS_PENDING = 'pending';
    public const IMAGE_STATUS_COMPLETED = 'completed';
    public const IMAGE_STATUS_FAILED = 'failed';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'url',
        'original_url',
        'domain',
        'title',
        'description',
        'cover_image_id',
        'cover_image_original_url',
        'type',
        'image_status',
        'is_favorite',
        'is_archived',
        'archived_at',
    ];

    protected $attributes = [
        'type' => self::TYPE_LINK,
        'image_status' => self::IMAGE_STATUS_PENDING,
        'is_favorite' => false,
        'is_archived' => false,
    ];

    protected function casts(): array
    {
        return [
            'is_favorite' => 'boolean',
            'is_archived' => 'boolean',
            'archived_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('brainkeep-models.user_model', 'App\\Models\\User'));
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'cover_image_id');
    }
}
