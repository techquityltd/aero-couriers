<div class="courier-form card w-full my-4 hidden">
    <h3 class="mb-px">Courier</h3>
    <div class="mt-4 flex">
        @isset($services)
            <div class="w-1/3">
                <div class="px-2 mb-4">
                    <label for="courier-service" class="block">Service</label>
                    <div class="select w-full">
                        <select id="courier-service" name="service" class="w-full"
                            @if (isset($fulfillment) && !$fulfillment->isOpen()) disabled @endif>
                            <option value="">Manual</option>
                            @foreach ($services as $carrier => $group)
                                @foreach ($group as $groupKey => $data)
                                    <optgroup data-courier="{{ $carrier }}" label="{{ $groupKey }}">
                                        @foreach ($data as $key => $item)
                                            <option data-courier="{{ $carrier }}" value="{{ $key }}" class="hidden" disabled
                                                @if ($selectedService === $key) selected @endif>
                                                {{ $item }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endisset

        @isset($connectors)
            <div class="w-1/3">
                <div class="px-2 mb-4">
                    <label for="courier-connector" class="block">Connector</label>
                    <div class="select w-full">
                        <select id="courier-connector" name="connector" class="w-full"
                            @if (isset($fulfillment) && !$fulfillment->isOpen()) disabled @endif>
                            <option value="">Manual</option>
                            @foreach ($connectors as $carrier => $group)
                                @foreach ($group as $key => $item)
                                    <option data-courier="{{ $carrier }}" value="{{ $key }}" class="hidden"
                                        @if ($selectedConnector === $key) selected @endif>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endisset
    </div>
</div>

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', () => {

            const availableServices = document.querySelectorAll("[data-courier]");

            // Define form elements.
            const courierForm = document.getElementsByClassName("courier-form")[0]
            const trackingForm = document.getElementsByClassName("tracking-form")[0];

            // Disable tracking and enable courier selection.
            function toggleCourierMode(on = false, courier = null) {

                toggleCourierForm(on);
                toggleTrackingForm(on);

                // rebuild the selectable options.
                availableServices.forEach(element => {
                    if (element.dataset.courier === courier) {
                        element.classList.remove("hidden");
                        element.disabled = false;
                    } else {
                        element.classList.add("hidden");
                        element.disabled = true;
                    }
                });
            }

            // Disable the option to disable courier selections.
            function disableCourierOptions()
            {
                document.getElementById("courier-service").disabled = true;
                document.getElementById("courier-connector").disabled = true;
            }

            // Enable or disable the courier form.
            function toggleCourierForm(on = false)
            {
                if (!courierForm) {
                    return;
                }

                courierForm.classList.toggle("hidden", !on);
            }

             // Enable or disable the tracking form.
            function toggleTrackingForm(on = false)
            {
                if (!trackingForm) {
                    return;
                }

                trackingForm.classList.toggle("hidden", on);
            }

             // Set the default options for courier settings.
            function setSelectedCourierOptions(fulfillmentMethod) {
                ["service", "connector"].forEach(type => {
                    let selector = document.querySelector("#courier-" + type);
                    selector.value = fulfillmentMethod[type];
                });
            }

            // Fulfillment method configuration.
            @isset($courierDrivers)
                const courierDrivers = @json($courierDrivers);
                const driverSelector = document.getElementById("driver");

                if (courierDrivers.length) {
                    courier = courierDrivers.find(element => element == driverSelector.value);
                    toggleCourierMode(courier !== undefined, courier);

                    driverSelector.addEventListener("change", function(event) {
                        courier = courierDrivers.find(element => element == event.target.value);
                        toggleCourierMode(courier !== undefined, courier);
                    });
                }
            @endisset

            // New fulfillment configuration.
            @isset($courierMethods)
                const courierMethods = @json($courierMethods);
                const methodSelector = document.getElementById("fulfillment-method");

                // Build the override link..
                const fulfillmentMethodLabel = document.querySelector("label[for='fulfillment-method']");

                var overrideLink = document.createElement('a');
                var linkText = document.createTextNode(@if(!request()->query('override-method'))"Override"@else"Default"@endif);
                overrideLink.appendChild(linkText);
                overrideLink.href = "{{ url()->current() }}" @if(!request()->query('override-method')) + "?override-method=true" @endif;
                overrideLink.classList = "ml-2 text-xs font-thin underline"
                fulfillmentMethodLabel.appendChild(overrideLink);

                if (Object.keys(courierMethods).length) {
                    methodSelector.addEventListener("change", function(event) {
                        fulfillmentMethod = courierMethods[event.target.value];
                        toggleCourierMode(fulfillmentMethod !== undefined, fulfillmentMethod?.driver);
                        setSelectedCourierOptions(fulfillmentMethod);
                    });

                    if (courierMethods[methodSelector.value] !== undefined) {
                        toggleCourierMode(true, courierMethods[methodSelector.value].driver);
                        setSelectedCourierOptions(courierMethods[methodSelector.value]);
                    }
                }
            @endisset

            // Existing fulfillment configuration.
            @isset($fulfillment)
                toggleCourierMode({{ $fulfillment->method->isCourier }}, "{{ $fulfillment->method->driver }}");

                @if(!$fulfillment->isOpen())
                    disableCourierOptions();
                @endif

                toggleTrackingForm(false);

                @if($shipment->committed && $fulfillment->state === 'successful')
                    document.getElementById("tracking-code").disabled = true;
                    document.getElementById("tracking-url").disabled = true;
                @endif
            @endisset
        });
    </script>
@endpush
