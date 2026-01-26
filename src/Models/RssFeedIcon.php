<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $feed_id
 * @property string $mime_type
 * @property string $data
 * @property string|null $hash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $data_uri
 *
 * @mixin \Illuminate\Database\Eloquent\Builder<RssFeedIcon>
 */
class RssFeedIcon extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'rss_feed_icons';

    protected $fillable = [
        'feed_id',
        'mime_type',
        'data',
        'hash',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function feed(): BelongsTo
    {
        return $this->belongsTo(RssFeed::class, 'feed_id');
    }

    /**
     * Get the data URI for embedding the icon.
     */
    public function getDataUriAttribute(): string
    {
        return "data:{$this->mime_type};base64,{$this->data}";
    }
}
