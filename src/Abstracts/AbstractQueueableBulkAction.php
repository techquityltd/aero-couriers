<?php

namespace Techquity\Aero\Couriers\Abstracts;

use Aero\Admin\Jobs\BulkActionJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Techquity\Aero\Couriers\CourierDriver;

abstract class AbstractQueueableBulkAction extends BulkActionJob implements ShouldQueue
{
    public function prepare(): void
    {
        $this->queue = CourierDriver::$queue;
    }
}
