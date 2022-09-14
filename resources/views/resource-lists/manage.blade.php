<dropdown-menu class="flex">
    <template v-slot:text="dropdown">
        <span class="mr-1 mt-1 text-sm font-medium cursor-pointer" @click.prevent="dropdown.toggle">Manage</span>
    </template>

    <template v-cloak>
        <ul class="list-reset py-2 leading-normal whitespace-no-wrap max-w-md font-bold">
            @can('couriers.manage-connectors')
                <li>
                    <a href="{{ route('admin.courier-manager.connectors.index', request()->all()) }}" class="block py-2 px-4 font-normal">Connectors</a>
                </li>
            @endcan
            @can('couriers.manage-services')
                <li>
                    <a href="{{ route('admin.courier-manager.services.index', request()->all()) }}" class="block py-2 px-4 font-normal">Services</a>
                </li>
            @endcan
            @can('couriers.manage-collections')
                <li>
                    <a href="{{ route('admin.courier-manager.collections.index', request()->all()) }}" class="block py-2 px-4 font-normal">Collections</a>
                </li>
            @endcan
            @can('couriers.manage-printers')
                <li>
                    <a href="{{ route('admin.courier-manager.printers.index', request()->all()) }}" class="block py-2 px-4 font-normal">Printers</a>
                </li>
            @endcan
        </ul>
    </template>
</dropdown-menu>
