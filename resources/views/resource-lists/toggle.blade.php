@can('couriers.manage-printers')
    <a href="{{ $route }}" onclick="event.preventDefault(); document.getElementById('{{ $key }}').submit();">
        <div class="bg-grey-lighter rounded-full py-1 px-1 whitespace-no-wrap inline-block orb" @isset($title) title="{{ $title }}" @endisset>
            <span class="inline-block w-orb h-orb rounded-full align-middle @if($active) bg-success @else bg-grey @endif"></span>
        </div>
    </a>

    <form id="{{ $key }}" action="{{ $route }}" method="POST" style="display: none;">
        @csrf
        @method('PUT')
    </form>
@else
    <div class="bg-grey-lighter rounded-full py-1 px-1 whitespace-no-wrap inline-block orb" @isset($title) title="{{ $title }}" @endisset>
        <span class="inline-block w-orb h-orb rounded-full align-middle @if($active) bg-success @else bg-grey @endif"></span>
    </div>
@endcan
