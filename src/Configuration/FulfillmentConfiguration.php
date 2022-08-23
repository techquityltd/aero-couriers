<?php

namespace Techquity\Aero\Couriers\Configuration;

use Aero\Admin\Utils\SettingHelpers;
use Aero\Common\Facades\Settings;
use Aero\Common\Settings\SettingGroup;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Techquity\Aero\Couriers\Abstracts\AbstractCourierConfiguration;

class FulfillmentConfiguration extends AbstractCourierConfiguration
{
    /**
     * The Fulfillment Method model.
     */
    protected ?FulfillmentMethod $fulfillmentMethod = null;

    /**
     * The Fulfillment model.
     */
    protected ?Fulfillment $fulfillment;

    /**
     * The configuration saved to the fulfillment model.
     */
    protected array $configuration;

    /**
     * Create a new FulfillmentMethodConfiguration instance.
     *
     * @return void
     */
    public function __construct(FulfillmentMethod $fulfillmentMethod, ?Fulfillment $fulfillment = null, array $configuration = [])
    {
        $this->fulfillmentMethod = $fulfillmentMethod;
        $this->fulfillment = $fulfillment;
        $this->configuration = $configuration;

        $this->configureDrivers($this->fulfillmentMethod->driver);

        if ($this->hasCourierConfiguration()) {
            // Generate the configuration key
            $this->key = $this->generateKey();

            Settings::group($this->key, function (SettingGroup $group) {
                $this->driver::fulfillmentMethodSettings($group);
            });

            $this->group = Settings::getGroup($this->key);

            $this->settings = Collection::make($this->group->settings()->all())
                ->reject(function ($setting) {
                    return $setting->getSection() === AbstractCourierDriver::METHOD_ONLY_SECTION;
                })
                ->map(function ($setting) {
                    return $setting->value($this->{$setting->key()});
                });

            $this->refreshSettings();
        }
    }

    /**
     * Generate a key for the current fulfillment method.
     */
    protected function generateKey(): string
    {
        return Str::slug('courier-fulfillment-' . Str::random(8));
    }

    /**
     * Refresh the settings cache.
     */
    protected function refreshSettings(): void
    {
        /**
         * Must be a better way to do this, will need to speak with Aero.
         * For now to get the settings to work on the fulfillment page we need a file.
         *
         * suggest using setting value rather than cache or option to update cache.
         */
        Settings::save($this->key, SettingHelpers::formatDataForSaving($this->settings, $this->settings->map(fn ($setting) => $setting->get())->toArray()));
        Storage::delete("settings/{$this->key}.json");
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
            $this->configuration['settings'] = $validator->validated();
            $this->fulfillment->courier_configuration = $this->configuration;
            $this->fulfillment->save();
        }
    }

    public function __get($name)
    {
        // Check if the setting is available as part of the courier config...
        if ($this->fulfillment) {
            if (isset($this->configuration['settings'][$name])) {
                return $this->configuration['settings'][$name];
            }
        }

        // Use the method setting as a backup...
        return $this->fulfillmentMethod->courier_configuration->$name;
    }
}
