<?php

namespace Brainkeep\Models\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleTag extends Pivot
{
    use HasUuids;

    protected $table = 'article_tag';

    public $incrementing = false;

    protected $keyType = 'string';
}
