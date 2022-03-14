<div class="card mt-4 mb-4 w-full [ aero-accordion ]">
    <input type="checkbox" id="fulfillmentLogsOpen" aria-hidden="true">

    <label class="aero-accordion-label" for="fulfillmentLogsOpen" aria-hidden="true">
        <h3 class="m-0 p-0">Logs ({{ $fulfillment->logs->count() }})</h3>
    </label>

    <h3 class="visually-hidden">Logs ({{ $fulfillment->logs->count() }})</h3>

    <div class="[ aero-accordion-content ]">

        @forelse($fulfillment->logs as $log)
            <div class="mb-4 pt-4">
                <div>
                    <span class="font-medium">{{ $log->title }}</span> <span class="text-xs text-grey mr-2"> &bullet; <span title="{{ $log->created_at }}">{{ $log->created_at->format(setting('admin.short_date_format')) }} ({{ $log->created_at->diffForHumans() }})</span></span>
                        <span class="inline-block uppercase py-1 px-2 @if($log->type === 'error') bg-error @elseif($log->type === 'success') bg-success @else bg-primary @endif text-xxs text-white rounded-sm">{{ $log->type }}</span>
                </div>
                <div class="py-1">{{ $log->message }}</div>
            </div>
        @empty
            <div class="pt-4">No Logs</div>
        @endforelse
    </div>

</div>
