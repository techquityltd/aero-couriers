<?php

namespace Techquity\Aero\Couriers\Traits;

use Aero\Fulfillment\Models\FulfillmentAddress;

trait UsesShipmentFactory
{
    protected function orders()
    {
        return $this->shipment->orders;
    }

    protected function fulfillments()
    {
        return $this->shipment->fulfillments;
    }

    protected function address()
    {
        return $this->fulfillments()->first()->address;
    }

    protected function getRecipientAddress(): ?FulfillmentAddress
    {
        return $this->fulfillments()->first()->address;
    }

    protected function getRecipientNumber(): ?string
    {
        return $this->fulfillments()->pluck('mobile')->filter()->unique()->first();
    }

    protected function getRecipientEmail(): ?string
    {
        return $this->fulfillments()->pluck('email')->filter()->unique()->first();
    }
}
