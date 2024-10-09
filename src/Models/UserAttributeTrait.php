<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Unvurn\Reauth\Reauth;

trait UserAttributeTrait
{
    public function user(): BelongsTo
    {
        /** @var Model $this */
        return $this->belongsTo(Reauth::userModel(), 'user_id');
    }
}
