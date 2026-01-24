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
    public const TYPES = ['note', 'photo', 'quote', 'video', 'audio', 'link', 'question', 'book'];

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
        // Import tracking
        'statamic_id',
    ];

    protected $attributes = [
        'is_public' => false,
        'type' => 'note',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'rating' => 'integer',
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
}
