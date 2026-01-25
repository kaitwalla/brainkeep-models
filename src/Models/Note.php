<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Note extends Model
{
    use HasFactory, HasUuids;

    /**
     * Available note types.
     */
    public const TYPES = ['note', 'photo', 'quote', 'video', 'audio', 'link', 'question', 'book', 'media'];

    /**
     * Available book statuses.
     */
    public const BOOK_STATUSES = ['to-get', 'to-read', 'reading', 'read', 'reviewed'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'parent_id',
        'title',
        'slug',
        'content',
        'caption',
        'snark',
        'type',
        'is_public',
        'in_notes_feed',
        'published_at',
        // Quote fields
        'cite',
        'source_url',
        // Video fields
        'video_url',
        'video_type',
        // Audio fields
        'audio_url',
        'embed_code',
        'songwhip_url',
        // Link fields
        'link_url',
        'link_image_url',
        // Question fields
        'question_text',
        // Book fields
        'author',
        'rating',
        'synopsis',
        'book_status',
        'release_date',
        'release_notified',
        'affiliate_amazon',
        'affiliate_bookshop',
        'recommendation',
        'opinion',
        'commentary',
        'google_books_id',
        // Media diet fields
        'media_diet_type',
        'media_url',
        'media_metadata',
        // Import tracking
        'statamic_id',
    ];

    protected $attributes = [
        'is_public' => false,
        'in_notes_feed' => true,
        'type' => 'note',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'in_notes_feed' => 'boolean',
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'rating' => 'integer',
            'opinion' => 'integer',
            'media_metadata' => 'array',
            'release_date' => 'date',
            'release_notified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('brainkeep-models.user_model', 'App\\Models\\User'));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Note::class, 'parent_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'note_tag')
            ->using(NoteTag::class)
            ->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class)->orderBy('position');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class)->orderBy('position');
    }

    public function audios(): HasMany
    {
        return $this->hasMany(Audio::class)->orderBy('position');
    }
}
