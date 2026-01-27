<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property string $user_id
 * @property string|null $category_id
 * @property string $feed_type
 * @property string|null $feed_url
 * @property string|null $site_url
 * @property string $title
 * @property string|null $description
 * @property string|null $etag
 * @property string|null $last_modified
 * @property string $fetch_status
 * @property string|null $fetch_error
 * @property int $error_count
 * @property \Illuminate\Support\Carbon|null $last_fetched_at
 * @property \Illuminate\Support\Carbon|null $next_fetch_at
 * @property int $fetch_interval_minutes
 * @property bool $fetch_full_content
 * @property string $content_fetcher
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder<RssFeed>
 */
class RssFeed extends Model
{
    use HasFactory, HasUuids;

    /**
     * Available feed types.
     */
    public const FEED_TYPES = ['rss', 'newsletter'];

    /**
     * Available fetch statuses.
     */
    public const FETCH_STATUSES = ['pending', 'fetching', 'completed', 'failed'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'rss_feeds';

    protected $fillable = [
        'user_id',
        'category_id',
        'feed_type',
        'feed_url',
        'site_url',
        'title',
        'description',
        'etag',
        'last_modified',
        'fetch_status',
        'fetch_error',
        'error_count',
        'last_fetched_at',
        'next_fetch_at',
        'fetch_interval_minutes',
        'fetch_full_content',
    ];

    protected $attributes = [
        'feed_type' => 'rss',
        'fetch_status' => 'pending',
        'error_count' => 0,
        'fetch_interval_minutes' => 30,
        'fetch_full_content' => false,
    ];

    protected function casts(): array
    {
        return [
            'error_count' => 'integer',
            'fetch_interval_minutes' => 'integer',
            'fetch_full_content' => 'boolean',
            'last_fetched_at' => 'datetime',
            'next_fetch_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('brainkeep-models.user_model', 'App\\Models\\User'));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RssCategory::class, 'category_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(RssEntry::class, 'feed_id');
    }

    public function icon(): HasOne
    {
        return $this->hasOne(RssFeedIcon::class, 'feed_id');
    }
}
