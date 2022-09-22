<?php

namespace Techquity\Aero\Couriers\Abstracts;

use Techquity\Aero\Couriers\Contracts\Response;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Techquity\Aero\Couriers\Traits\HandlesClientResponse;

abstract class AbstractResponse implements Response
{
    use HandlesClientResponse;

    /**
     * The json request.
     */
    protected array $request;

    /**
     * The json decoded array.
     */
    protected array $array;

    /**
     * The original Guzzle response.
     */
    protected Psr7Response $response;

    /**
     * Create a new response instance.
     */
    public function __construct(array $request, Psr7Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->array = json_decode($this->body(), true) ?? [];
    }

    /**
     * Get the original array response data.
     */
    public function original(): array
    {
        return $this->array;
    }

    /**
     * Get the array response data.
     */
    public function array(): array
    {
        return $this->array;
    }

    /**
     * Get the failed error messages to be logged.
     */
    public function getFailedMessages(): array
    {
        return [];
    }
}
