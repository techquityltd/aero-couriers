@can('couriers.manage-services')
    <dropdown-menu class="flex">
        <template v-slot:text="dropdown">
            <span class="mr-1 mt-1 text-sm font-medium cursor-pointer" @click.prevent="dropdown.toggle">Manage</span>
        </template>

        <template v-cloak>
            <ul class="list-reset py-2 leading-normal whitespace-no-wrap max-w-md font-bold">
                <li>
                    <a href="{{ route('admin.courier-manager.services.store', request()->all()) }}" onclick="event.preventDefault(); document.getElementById('refresh-services').submit();" class="block py-2 px-4 font-normal">
                        Refresh Services
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.courier-manager.services.index', array_merge(['deleted' => true], request()->all())) }}" class="block py-2 px-4 font-normal">
                        Deleted Services
                    </a>
                </li>
            </ul>
        </template>
    </dropdown-menu>

    <form id="refresh-services" action="{{ route('admin.courier-manager.services.store', request()->all()) }}" method="POST" style="display: none;">
        @csrf
    </form>
@endcan
