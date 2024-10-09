<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;

/**
 * @property User $user
 */
interface UserAttributeInterface
{
    public function user(): BelongsTo;
}
