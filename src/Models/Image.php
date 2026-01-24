<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'note_id',
        'filename',
        'original_name',
        'alt_text',
        'mime_type',
        'size',
        'width',
        'height',
        'position',
    ];

    protected $appends = ['url'];

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
        return Storage::disk(config('filesystems.default'))->url($this->filename);
    }
}
