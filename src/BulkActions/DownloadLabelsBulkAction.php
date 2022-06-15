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
        $this->labelGroup = Str::random();
    }

    public function handle(): void
    {
        $merger = new Merger();

        $this->list->items()
            ->each(function (Fulfillment $fulfillment) use ($merger) {

                $fulfillment->items->first()->order->documents
                    ->filter(fn ($document) => (Str::startsWith($document->key, 'label_')))
                    ->reject(fn ($document) => ($document->failed))
                    ->each(function ($document) use ($merger) {
                        $merger->addRaw(
                            Storage::disk('local')->get(sprintf(OrderDocumentTemplate::$path . 'orders/%s.pdf', $document->key))
                        );
                    });
            });

        Storage::disk('local')->put(OrderDocumentTemplate::$path . "temp/labels/{$this->labelGroup}.pdf", $merger->merge());
    }

    public function response()
    {
        return Storage::disk('local')->download(OrderDocumentTemplate::$path . "temp/labels/{$this->labelGroup}.pdf");
    }
}
