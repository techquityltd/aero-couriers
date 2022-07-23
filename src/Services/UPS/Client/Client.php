<?php

namespace Techquity\Aero\Couriers\Services\UPS\Client;

use Techquity\Aero\Couriers\Client\Response;
use Illuminate\Support\Str;

class Client
{
    /**
     * The guzzle client interface.
     */
    protected \GuzzleHttp\ClientInterface $client;

    /**
     * The production API endpoint.
     */
    protected string $productionEndpoint = 'https://onlinetools.ups.com/';

    /**
     * The sandbox API endpoint.
     */
    protected string $sandboxEndpoint = 'https://wwwcie.ups.com/';

    /**
     * Fulfillment configuration.
     */
    protected array $config;

    /**
     * Create a new client instance.
     */
    public function __construct($config)
    {
        $this->config = $config;

        $this->client = new \GuzzleHttp\Client(
            $this->config()
        );
    }

    public function __call($name, $arguments)
    {
        return new Response($this->client->$name(...$arguments));
    }

    /**
     * Define the config required for this connection.
     */
    public function config(): array
    {
        return [
            'http_errors' => false,
            'base_uri' => $this->getBaseUri(),
            'headers' => array_merge([
                'AccessLicenseNumber' => $this->config['access_key'],
                'Username' => $this->config['username'],
                'Password' => $this->config['password'],
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'transId' => (string) Str::uuid(),
                'transactionSrc' => $this->config['transaction_source'],
            ])
        ];
    }

    /**
     * Get the base url for the current environment.
     */
    protected function getBaseUri()
    {
        return $this->config['server'] === 'production' ? $this->productionEndpoint : $this->sandboxEndpoint;
    }
}
