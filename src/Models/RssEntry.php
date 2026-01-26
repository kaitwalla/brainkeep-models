<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property string $feed_id
 * @property string $guid
 * @property string $guid_hash
 * @property string $url
 * @property string $title
 * @property string|null $author
 * @property string|null $content
 * @property string|null $full_content
 * @property string $full_content_status
 * @property string|null $summary
 * @property string $status
 * @property bool $is_starred
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder<RssEntry>
 */
class RssEntry extends Model
{
    use HasFactory, HasUuids;

    /**
     * Available read statuses.
     */
    public const STATUSES = ['unread', 'read'];

    /**
     * Available full content fetch statuses.
     */
    public const FULL_CONTENT_STATUSES = ['pending', 'fetching', 'completed', 'failed', 'skipped'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'rss_entries';

    protected $fillable = [
        'user_id',
        'feed_id',
        'guid',
        'guid_hash',
        'url',
        'title',
        'author',
        'content',
        'full_content',
        'full_content_status',
        'summary',
        'status',
        'is_starred',
        'published_at',
        'read_at',
    ];

    protected $attributes = [
        'status' => 'unread',
        'is_starred' => false,
        'full_content_status' => 'skipped',
    ];

    protected function casts(): array
    {
        return [
            'is_starred' => 'boolean',
            'published_at' => 'datetime',
            'read_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (RssEntry $entry) {
            // Generate guid_hash if not set
            if (empty($entry->guid_hash) && ! empty($entry->guid)) {
                $entry->guid_hash = hash('sha256', $entry->guid);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('brainkeep-models.user_model', 'App\\Models\\User'));
    }

    public function feed(): BelongsTo
    {
        return $this->belongsTo(RssFeed::class, 'feed_id');
    }
}
