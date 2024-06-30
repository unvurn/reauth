<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $abilities
 * @property mixed $created_at
 * @property mixed $expires_at
 */
class AccessToken extends Model implements UserAttributeInterface
{
    use UserAttributeTrait;

    protected $casts = [
        'abilities' => 'json',
        'last_accessed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
    ];

    protected $hidden = [
        'token',
    ];

    public function can(string $ability): bool
    {
        return in_array('*', $this->abilities) || array_key_exists($ability, array_flip($this->abilities));
    }

    public function cant(string $ability): bool
    {
        return !$this->can($ability);
    }

    public function touchAccessed(): void
    {
        if (method_exists($this->getConnection(), 'hasModifiedRecords') &&
            method_exists($this->getConnection(), 'setRecordModificationState')) {
            tap($this->getConnection()->hasModifiedRecords(), function ($hasModifiedRecords) {
                $this->forceFill(['last_accessed_at' => now()])->save();

                $this->getConnection()->setRecordModificationState($hasModifiedRecords);
            });
        } else {
            $this->forceFill(['last_accessed_at' => now()])->save();
        }
    }

    public function isAvailable(?int $expiration): bool
    {
        return (!$expiration || $this->created_at->gt(now()->subMinutes($expiration)))
            && (!$this->expires_at || !$this->expires_at->isPast());
    }
}
