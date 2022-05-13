@foreach ($types as $label => $type)
    <div class="w-1/2 px-2 mb-4">
        @if ($type === 'string')
            <label class="block">{{ str_replace('_', ' ', Str::title($label)) }}</label>
            <input type="text" id="{{ $label }}" name="configuration[{{ $courier }}][{{ $label }}]" value="{{ old("configuration.{$courier}.{$label}", $model ? $model->courierConfig($label, $courier) : '') }}" class="w-full {{ $errors->has($label) ? 'has-error' : '' }}">
        @endif

        @if ($type === 'encrypted')
            <label for="{{ $label }}" class="block">{{ str_replace('_', ' ', Str::title($label)) }}</label>
            <input type="password" id="{{ $label }}" name="configuration[{{ $courier }}][{{ $label }}]" value="{{ old("configuration.{$courier}.{$label}", $model ? $model->courierConfig($label, $courier) : '') }}" class="w-full {{ $errors->has($label) ? 'has-error' : '' }}">
        @endif

        @if ($type === 'boolean')
            <div class="block mb-2">&nbsp;</div>
            <label for="{{ $label }}">
                <label class="checkbox">
                    <input id="{{ $label }}" type="checkbox" name="configuration[{{ $courier }}][{{ $label }}]" value="1" {{ old("configuration.{$courier}.{$label}", $model ? $model->courierConfig($label, $courier) : '') ? 'checked' : '' }}>
                    <span></span>
                </label>
                {{ str_replace('_', ' ', Str::title($label)) }}
            </label>
        @endif

        @if (is_array($type))
            @isset($type['select'])
                <div class="px-2 mb-4">
                    <label for="{{ $label }}" class="block">{{ str_replace('_', ' ', Str::title($label)) }}</label>
                    <div class="select w-full">
                        <select id="{{ $label }}" name="configuration[{{ $courier }}][{{ $label }}]" class="w-full">
                            @foreach ($type['select'] as $option)
                                <option value="{{ $option }}" {{ old("configuration.{$courier}.{$label}", $model ? $model->courierConfig($label, $courier) : '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
    @endif
    </div>
    @php
        $parcels = ['amount' => 1, 'weights' => []];
    @endphp
    <div class="w-full px-2 mb-4">
        @if ($type === 'special.parcels')
            <split-consignment courier="{{ $courier }}" label="{{ $label }}" weight-unit="{{ setting('courier.default_weight', 'g') }}" :parcels="{{ json_encode(old("configuration.{$courier}.{$label}", isset($fulfillment) ? $fulfillment->courierConfig('parcels', $method->courier, $parcels) : $parcels)) }}" />
        @endif
    </div>

    @push('scripts')
        <script src="{{ asset(mix('components.js', 'modules/techquity/couriers')) }}"></script>
        <script>
            window.AeroAdmin.vue.use(window.couriersComponents);
        </script>
    @endpush
    @endforeach
