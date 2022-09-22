<?php

namespace Techquity\Aero\Couriers\Contracts;

use Techquity\Aero\Couriers\Abstracts\AbstractResponse;

interface Request
{
    /**
     * Get the base url for the current environment.
     */
    public function getBaseUri(): string;

    /**
     * Define the config required for this connection.
     */
    public function config(): array;

    /**
     * Dynamically call the client request.
     */
    public function __call(string $name, array $arguments): AbstractResponse;
}
