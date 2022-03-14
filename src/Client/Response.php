<?php

namespace Techquity\Aero\Couriers\Client;

class Response
{
    /**
     * The Guzzle response.
     */
    protected $response;

    /**
     * The json decoded in an object type.
     */
    protected $object;

    /**
     * The json decoded in an array type.
     */
    protected $array;

    /**
     * Create a new response instance.
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * Get the body of the response.
     */
    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    public function array(): array
    {
        return $this->array ?: json_decode($this->body(), true);
    }

    /**
     * Get the json decoded response body.
     */
    public function object(): object
    {
        return $this->object ?: json_decode($this->body(), false);
    }

    /**
     * Get a collection from the the response json.
     */
    public function collect(): \Illuminate\Support\Collection
    {
        return collect($this->object());
    }

    /**
     * Get the headers from the response.
     */
    public function headers(): array
    {
        return $this->response->getHeaders();
    }

    /**
     * Get the status code of the response.
     */
    public function status(): int
    {
        return (int) $this->response->getStatusCode();
    }

    /**
     * Get value from the decoded response.
     */
    public function get(string $key, $default = null)
    {
        return data_get($this->object(), $key, $default);
    }

    /**
     * Check if the response was successful.
     */
    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    /**
     * Handle the successful event.
     */
    public function onSuccessful(callable $callback)
    {
        if ($this->successful()) {
            $callback($this);
            return $this;
        }

        return $this;
    }

    /**
     * Handle the failed response.
     */
    public function onFailure(callable $callback)
    {
        if (!$this->successful()) {
            $callback($this);
            return $this;
        }

        return $this;
    }

    public function finally(callable $callback)
    {
        $callback($this);
        return $this;
    }
}
