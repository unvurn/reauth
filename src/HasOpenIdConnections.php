<?php

declare(strict_types=1);

namespace Unvurn\Reauth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @method morphMany(string $class, string $string)
 */
trait HasOpenIdConnections
{
    public function connections(): MorphMany
    {
        return $this->morphMany(Reauth::openIdConnectionModel(), 'user');
    }

    public function createOpenIdConnection(string $issuer, string $subject): Model
    {
        return $this->connections()->create([
            'issuer' => $issuer,
            'subject' => $subject,
        ]);
    }
}
