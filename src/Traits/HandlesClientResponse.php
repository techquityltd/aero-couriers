<?php

namespace Techquity\Aero\Couriers\Traits;

trait HandlesClientResponse
{
    /**
     * Get the json request data.
     */
    public function request(): array
    {
        return $this->request;
    }

    /**
     * Get the headers from the response.
     */
    public function headers(): array
    {
        return $this->response->getHeaders();
    }

    /**
     * Get the body of the response.
     */
    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    /**
     * Get the status code of the response.
     */
    public function status(): int
    {
        return (int) $this->response->getStatusCode();
    }

    /**
     * Check if the response was successful.
     */
    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    /**
     * Get value from the decoded response.
     */
    public function get(string $key, $default = null)
    {
        $value = data_get($this->array(), $key, $default);

        return is_array($value) ? reset($value) : $value;
    }

    /**
     * Callable method which is available regardless of status.
     */
    public function always(callable $callback): self
    {
        $callback($this);

        return $this;
    }

    /**
     * Handle the successful event.
     */
    public function onSuccessful(callable $callback)
    {
        if ($this->successful()) {
            $callback($this);
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
        }

        return $this;
    }
}
