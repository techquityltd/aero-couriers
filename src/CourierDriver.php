<?php

namespace Techquity\Aero\Couriers;

use Aero\Fulfillment\Contracts\Response;
use Aero\Fulfillment\FulfillmentDriver;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Responses\FulfillmentResponse;
use Techquity\Aero\Couriers\Jobs\ProcessPendingFulfillment;
use Techquity\Aero\Couriers\Models\FulfillmentLog;

class CourierDriver extends FulfillmentDriver
{
    /**
     * The name of the fulfillment driver.
     */
    public const NAME = 'courier';

    /**
     * The default fulfillment state of a fulfillment when created.
     *
     * @return string
     */
    public function getDefaultState(): string
    {
        return Fulfillment::OPEN;
    }

    /**
     * Make the fulfillment request.
     *
     * @return \Aero\Fulfillment\Contracts\Response
     */
    public function handle(): Response
    {
        $admin = auth()->user()->name;

        $this->fulfillments->each(function ($fulfillment) use ($admin) {
            if (in_array($fulfillment->state, [
                Fulfillment::OPEN,
                Fulfillment::FAILED,
                Fulfillment::CANCELED,
            ])) {
                $fulfillment->logs()->create([
                    'type' => FulfillmentLog::INFO,
                    'title' => 'Fulfillment Processed',
                    'message' => "Fulfillment processed by {$admin}",
                ]);

                $fulfillment->state = Fulfillment::PENDING;
                $fulfillment->save();

                ProcessPendingFulfillment::dispatch($fulfillment)->onQueue('courier');
            }
        });

        $response = new FulfillmentResponse();

        $response->setSuccessful(true);

        return $response;
    }
}
