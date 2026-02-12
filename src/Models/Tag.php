<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'name',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('brainkeep-models.user_model', 'App\\Models\\User'));
    }

    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(Note::class, 'note_tag')
            ->using(NoteTag::class)
            ->withTimestamps();
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tag')
            ->using(ArticleTag::class)
            ->withTimestamps();
    }
}
