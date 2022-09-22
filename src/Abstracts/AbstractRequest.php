<?php

namespace Techquity\Aero\Couriers\Abstracts;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Techquity\Aero\Couriers\Contracts\Request;
use Techquity\Aero\Couriers\Models\CourierConnector;

abstract class AbstractRequest implements Request
{
    /**
     * The guzzle client interface.
     */
    protected ClientInterface $client;

    /**
     * The production API endpoint.
     */
    protected string $productionEndpoint;

    /**
     * The sandbox API endpoint.
     */
    protected string $sandboxEndpoint;

    /**
     * Courier connector.
     */
    protected CourierConnector $connector;

    /**
     * Create a new client instance.
     */
    public function __construct(CourierConnector $connector)
    {
        $this->connector = $connector;

        $this->client = new Client(
            $this->config()
        );
    }
}
