<?php

declare(strict_types=1);

namespace Unvurn\Reauth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method hasMany(string $class, string $string)
 */
trait HasOpenIdConnections
{
    public function connections(): HasMany
    {
        return $this->hasMany(Reauth::openIdConnectionModel(), 'user_id');
    }

    public function createOpenIdConnection(string $issuer, string $subject): Model
    {
        return $this->connections()->create([
            'issuer' => $issuer,
            'subject' => $subject,
        ]);
    }
}
