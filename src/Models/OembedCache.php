<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Cached oEmbed data for social media embeds.
 *
 * @property string $id
 * @property string $url
 * @property string $url_hash
 * @property string $provider
 * @property string|null $html
 * @property string|null $author_name
 * @property string|null $author_url
 * @property int|null $width
 * @property int|null $height
 * @property string $cache_status
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $fetched_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder<OembedCache>
 */
class OembedCache extends Model
{
    use HasUuids;

    public const PROVIDER_MASTODON = 'mastodon';
    public const PROVIDER_BLUESKY = 'bluesky';

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'oembed_cache';

    protected $fillable = [
        'url',
        'url_hash',
        'provider',
        'html',
        'author_name',
        'author_url',
        'width',
        'height',
        'cache_status',
        'error_message',
        'fetched_at',
    ];

    protected $attributes = [
        'cache_status' => self::STATUS_PENDING,
    ];

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'fetched_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Generate URL hash for lookup.
     */
    public static function hashUrl(string $url): string
    {
        return hash('sha256', $url);
    }

    /**
     * Find by URL using hash lookup.
     */
    public static function findByUrl(string $url): ?self
    {
        return self::where('url_hash', self::hashUrl($url))->first();
    }

    /**
     * Check if the cache entry is stale (older than 24 hours).
     */
    public function isStale(): bool
    {
        if (!$this->fetched_at) {
            return true;
        }

        return $this->fetched_at->lt(now()->subHours(24));
    }
}
