<?php

namespace Techquity\Aero\Couriers\Services\FedEx\Client;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Techquity\Aero\Couriers\Client\Response;

class Client
{
    /**
     * The guzzle client interface.
     */
    protected \GuzzleHttp\ClientInterface $client;

    /**
     * The production API endpoint.
     */
    protected string $productionEndpoint = 'https://apis.fedex.com/';

    /**
     * The sandbox API endpoint.
     */
    protected string $sandboxEndpoint = 'https://apis-sandbox.fedex.com/';

    /**
     * The Bearer token.
     */
    protected string $token;

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
            $this->config($config)
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
            'headers' => $this->getAuthorization()
        ];
    }

    /**
     * Get the base url for the current environment.
     */
    protected function getBaseUri()
    {
        return strtolower($this->config['server']) === strtolower('production') ? $this->productionEndpoint : $this->sandboxEndpoint;
    }

    /**
     * Get the bearer token and return Authorization
     */
    protected function getAuthorization(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->authorize()
        ];
    }

    /**
     * Authorize the current req
     */
    protected function authorize(): string
    {
        return $this->token = Cache::remember('fedex_token', now()->addMinutes(59), fn () => $this->fetchToken());
    }

    /**
     * Make a request for the bearer token.
     */
    protected function fetchToken(): string
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->getBaseUri(),
            'http_errors' => false
        ]);

        $response = $client->post('oauth/token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->config['api_key'],
                'client_secret' => $this->config['secret_key'],
            ]
        ]);

        return (new Response($response))
            ->onFailure(function (Response $response) {
                Log::error('failed to get fedex API bearer token', $response->array());

                throw new RuntimeException('Failed to get FedEx bearer token.');
            })
            ->get('access_token');
    }
}
