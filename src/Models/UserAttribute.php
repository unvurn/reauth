<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property mixed $issuer
 * @property mixed $subject
 * @property mixed $user_id
 * @method static create(array $array)
 * @method static where(array $array)
 */
class UserAttribute extends Model implements UserAttributeInterface
{
    use HasFactory;
    use UserAttributeTrait;

    protected $fillable = [
        'user_id',
        'key',
        'value'
    ];
}
