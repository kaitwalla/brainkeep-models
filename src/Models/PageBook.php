<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PageBook extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'page_id',
        'title',
        'description',
        'cover_image_id',
        'amazon_url',
        'epub_path',
        'sort_order',
    ];

    protected $attributes = [
        'sort_order' => 0,
    ];

    protected $appends = ['epub_url'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'cover_image_id');
    }

    public function getEpubUrlAttribute(): ?string
    {
        if (!$this->epub_path) {
            return null;
        }

        $disk = Storage::disk(config('filesystems.default'));
        $diskDriver = config('filesystems.disks.' . config('filesystems.default') . '.driver');

        if ($diskDriver === 'local') {
            return url('/storage/' . $this->epub_path);
        }

        return $disk->url($this->epub_path);
    }
}
