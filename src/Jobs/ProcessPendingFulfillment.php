<?php

namespace Techquity\Aero\Couriers\Jobs;

use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Techquity\Aero\Couriers\Facades\Courier;

class ProcessPendingFulfillment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * The fulfillment model.
     */
    protected Fulfillment $fulfillment;

    /**
     * Create a new job instance.
     */
    public function __construct(Fulfillment $fulfillment)
    {
        $this->fulfillment = $fulfillment->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $driver = Courier::forFulfillment($this->fulfillment);

        if ($this->fulfillment->method->courier) {
            $driver->createConsignment();
        }
    }
}
