<?php

namespace Techquity\Aero\Couriers\Commands;

use Illuminate\Console\Command;
use Techquity\Aero\Couriers\Models\CourierShipment;

class ClearOldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'couriers:cleardown';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old request/response data from the courier shipments table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $dateBefore = now()->subDays(setting('couriers.log_retention_days'));

        CourierShipment::where('created_at', '<=', $dateBefore)->update([
            'request' => null,
            'response' => null,
        ]);
    }
}
