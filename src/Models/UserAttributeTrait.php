<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

trait UserAttributeTrait
{
    public function user(): MorphTo
    {
        /** @var Model $this */
        return $this->morphTo('user');
    }
}
