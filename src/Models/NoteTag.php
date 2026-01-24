<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class NoteTag extends Pivot
{
    use HasUuids;

    protected $table = 'note_tag';

    public $incrementing = false;

    protected $keyType = 'string';
}
