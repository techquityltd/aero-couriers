<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use Aero\Admin\ResourceLists\FulfillmentsResourceList;
use Aero\Fulfillment\Models\Fulfillment;
use iio\libmergepdf\Merger;
use Illuminate\Support\Facades\Storage;
use Techquity\Aero\OrderDocuments\Models\OrderDocumentTemplate;
use Illuminate\Support\Str;

class DownloadLabelsBulkAction extends BulkActionJob
{
    protected FulfillmentsResourceList $list;

    protected string $admin;

    public function __construct(FulfillmentsResourceList $list)
    {
        $this->list = $list;
        $this->admin = auth()->user()->name;
        $this->downloadName = Str::random(5);
    }

    public function handle()
    {
        $merger = new Merger();

        $this->list->items()->each(function (Fulfillment $fulfillment) use ($merger) {

            $key = "label_{$fulfillment->method->courier}_{$fulfillment->reference}";

            if (Storage::disk('local')->exists(OrderDocumentTemplate::$path . "orders/{$key}.pdf")) {
                $merger->addRaw(
                    Storage::disk('local')->get(sprintf(OrderDocumentTemplate::$path . "orders/{$key}.pdf"))
                );
            }
        });

        Storage::disk('local')->put(OrderDocumentTemplate::$path . "temp/{$this->downloadName}.pdf", $merger->merge());
    }

    /**
     * The successful response to return.
     */
    public function response()
    {
        return Storage::disk('local')->download(OrderDocumentTemplate::$path . "temp/{$this->downloadName}.pdf");
    }
}
