<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use iio\libmergepdf\Merger;
use Illuminate\Support\Facades\Storage;
use Techquity\Aero\Couriers\Models\CourierShipment;
use Techquity\Aero\Couriers\ResourceLists\CourierShipmentsResourceList;

class DownloadLabelsBulkAction extends BulkActionJob
{
    protected $list;

    protected $path;

    public function __construct(CourierShipmentsResourceList $list)
    {
        $this->list = $list;

        $this->path = 'labels/' . date('Y_m_d_His') . '_bulk.php';
    }

    public function handle()
    {
        $merger = new Merger();

        $this->list->items()->each(function (CourierShipment $shipment) use ($merger) {
            /**
             * Can only merge PDF files
             * For others we may need to add the option to download as a zip
             */
            if (pathinfo(Storage::path($shipment->label), PATHINFO_EXTENSION) !== 'pdf') {
                return;
            }

            return $merger->addFile(Storage::path($shipment->label));
        });

        Storage::put($this->path, $merger->merge());

        return Storage::download($this->path);
    }

    public function response()
    {
        return Storage::download($this->path);
    }

    public function __destruct()
    {
        Storage::delete($this->path);
    }
}
