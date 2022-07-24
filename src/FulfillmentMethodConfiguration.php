<?php

namespace Techquity\Aero\Couriers;

use Aero\Admin\Utils\SettingHelpers;
use Aero\Common\Facades\Settings;
use Aero\Common\Settings\SettingGroup;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FulfillmentMethodConfiguration extends AbstractCourierConfiguration
{
    /**
     * The Fulfillment Method model.
     */
    protected ?FulfillmentMethod $fulfillmentMethod = null;

    /**
     * Create a new FulfillmentMethodConfiguration instance.
     *
     * @return void
     */
    public function __construct(string $driver, ?FulfillmentMethod $fulfillmentMethod = null)
    {
        $this->fulfillmentMethod = $fulfillmentMethod;

        $this->configureDrivers($driver);

        if ($this->hasCourierConfiguration()) {
            // Generate the configuration key
            $this->key = $this->generateKey();

            Settings::group($this->key, function (SettingGroup $group) {
                $this->driver::fulfillmentMethodSettings($group);
            });

            $this->group = Settings::getGroup($this->key);

            $this->settings = Collection::make($this->group->settings()->all());
        }
    }

    /**
     * Generate a key for the current fulfillment method.
     */
    protected function generateKey(): string
    {
        return Str::slug('courier-fulfillment-method-' . optional($this->fulfillmentMethod)->id ?? 'x');
    }

    /**
     * Save the fulfillment method configuration as an Aero setting file.
     */
    public function save(Request $request): void
    {
        if (!$this->hasCourierConfiguration()) {
            return;
        }

        $validator = $this->validator($request);

        if (!$validator->fails()) {
            Settings::save($this->key, SettingHelpers::formatDataForSaving($this->settings, $validator->validated()));
        }
    }

    public function __get($name)
    {
        return setting("{$this->key}.{$name}");
    }
}
