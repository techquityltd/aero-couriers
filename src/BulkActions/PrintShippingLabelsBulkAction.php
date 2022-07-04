<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use Aero\Admin\ResourceLists\OrdersResourceList;
use Aero\Cart\Models\Order;
use Aero\Fulfillment\Models\Fulfillment;
use iio\libmergepdf\Merger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Techquity\Aero\OrderDocuments\Models\OrderDocumentTemplate;

class PrintShippingLabelsBulkAction extends BulkActionJob
{
    protected OrdersResourceList $list;

    protected string $admin;

    public function __construct(OrdersResourceList $list)
    {
        $this->admin = auth()->user()->name;
        $this->list = $list;
        $this->labelGroup = Str::random();
    }

    public function handle(): void
    {
        $merger = new Merger();

        $this->list->items()
            ->each(function (Order $order) use ($merger) {
                $order->documents
                    ->filter(fn ($document) => (Str::startsWith($document->key, 'label_')))
                    ->reject(fn ($document) => ($document->failed))
                    ->each(function ($document) use ($merger) {
                        $merger->addRaw(
                            Storage::disk('local')->get(sprintf(OrderDocumentTemplate::$path . 'orders/%s.pdf', $document->key))
                        );
                    });
            });

        Storage::disk('local')->put(OrderDocumentTemplate::$path . "temp/labels/{$this->labelGroup}.pdf", $merger->merge());

        $this->list->items()
            ->each(function ($order) {
                $order->fulfillments->where('state', 'pushed')->each->update(['state' => Fulfillment::SUCCESSFUL]);
            });
    }

    public function response()
    {
        return Storage::disk('local')->download(OrderDocumentTemplate::$path . "temp/labels/{$this->labelGroup}.pdf");
    }
}
