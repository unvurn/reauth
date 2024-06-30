<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User;

/**
 * @property User $user
 */
interface UserAttributeInterface
{
    public function user(): MorphTo;
}
