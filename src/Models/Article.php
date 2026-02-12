<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory, HasUuids;

    /**
     * Extraction status values.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_PENDING_EXTRACTOR = 'pending_extractor';

    /**
     * Average reading speed in words per minute.
     */
    public const WORDS_PER_MINUTE = 200;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'url',
        'original_url',
        'domain',
        'title',
        'author',
        'excerpt',
        'content',
        'word_count',
        'estimated_reading_time',
        'lead_image_id',
        'is_public',
        'is_read',
        'is_favorite',
        'is_archived',
        'reading_progress',
        'scroll_position',
        'extraction_status',
        'extraction_error',
        'published_at',
        'saved_at',
        'read_at',
        'archived_at',
        'media_diet_entry_id',
    ];

    protected $attributes = [
        'is_public' => false,
        'is_read' => false,
        'is_favorite' => false,
        'is_archived' => false,
        'reading_progress' => 0.0,
        'scroll_position' => 0,
        'extraction_status' => self::STATUS_PENDING,
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'is_read' => 'boolean',
            'is_favorite' => 'boolean',
            'is_archived' => 'boolean',
            'reading_progress' => 'float',
            'scroll_position' => 'integer',
            'word_count' => 'integer',
            'estimated_reading_time' => 'integer',
            'published_at' => 'datetime',
            'saved_at' => 'datetime',
            'read_at' => 'datetime',
            'archived_at' => 'datetime',
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

        static::creating(function (Article $article) {
            // Default saved_at to current time if not set
            if ($article->saved_at === null) {
                $article->saved_at = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('brainkeep-models.user_model', 'App\\Models\\User'));
    }

    public function leadImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'lead_image_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag')
            ->using(ArticleTag::class)
            ->withTimestamps();
    }

    public function contentImages(): HasMany
    {
        return $this->hasMany(ArticleImage::class)->orderBy('position');
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(Note::class)->where('type', 'highlight')->orderBy('created_at');
    }

    /**
     * Calculate estimated reading time from word count.
     */
    public static function calculateReadingTime(int $wordCount): int
    {
        return (int) ceil($wordCount / self::WORDS_PER_MINUTE);
    }
}
