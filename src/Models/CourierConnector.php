<?php

namespace Techquity\Aero\Couriers\Models;

use Aero\Common\Models\Model;

class CourierConnector extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'url',
        'user',
        'password',
        'token',
        'carrier'
    ];

    /**
     * Get the connectors decrypted password.
     */
    public function getPasswordAttribute(string $value): string
    {
        return decrypt($value);
    }

    /**
     * Set the connectors encrypted password
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = encrypt($value);
    }

    /**
     * Get the connectors decrypted token.
     */
    public function getTokenAttribute(string $value): string
    {
        return decrypt($value);
    }

    /**
     * Set the connectors encrypted token.
     */
    public function setTokenAttribute(string $value): void
    {
        $this->attributes['token'] = encrypt($value);
    }

    /**
     * Scope the available connectors for selectable dropdown.
     */
    public function scopeDisplayAvailable($query)
    {
        return $query
            ->orderBy('carrier')
            ->cursor()
            ->mapToGroups(fn ($connector) => [$connector->carrier => $connector])
            ->map(fn ($group) => $group->mapWithKeys(fn ($connector) => [
                $connector->id => $connector->name ?? $connector->description
            ]))
            ->toArray();
    }
}
