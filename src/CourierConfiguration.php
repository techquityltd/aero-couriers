<?php

namespace Techquity\Aero\Couriers;

use Aero\Admin\Utils\SettingHelpers;
use Aero\Common\Facades\Settings;
use Aero\Common\Settings\Setting;
use Aero\Common\Settings\SettingGroup;
use Aero\Fulfillment\FulfillmentProcessor;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourierConfiguration
{
    /**
     * The name for a method only setting.
     */
    public const METHOD_ONLY = 'method';

    /**
     * The settings group key.
     */
    protected string $key;

    /**
     * The courier driver.
     */
    protected string $courier;

    /**
     * Array of available drivers.
     */
    protected array $drivers;

    /**
     * The Fulfillment Method model.
     */
    protected ?FulfillmentMethod $fulfillmentMethod;

    /**
     * The Fulfillment model.
     */
    protected ?Fulfillment $fulfillment;

    /**
     * The setting group instance.
     */
    protected SettingGroup $group;

    /**
     * Collection of settings.
     */
    protected Collection $settings;

    /**
     * Create a new courier configuration instance.
     */
    public function __construct(string $courier, ?FulfillmentMethod $fulfillmentMethod = null, ?Fulfillment $fulfillment = null)
    {
        $this->courier = $courier;
        $this->fulfillmentMethod = $fulfillmentMethod;
        $this->fulfillment = $fulfillment;

        // Get available drivers...
        $this->drivers = FulfillmentProcessor::getDrivers();

        if ($this->isValid()) {
            // Generate the configuration key
            $this->key = $this->generateKey();

            // We create this now so the setting are only available here...
            Settings::group($this->key, function (SettingGroup $group) {
                $this->drivers[$this->courier]::fulfillmentMethodSettings($group);
            });

            $this->group = Settings::getGroup($this->key);

            $this->settings = Collection::make($this->group->settings()->all());

            if ($this->fulfillment) {
                $this->settings = $this->settings->map(function (Setting $setting) {
                    // Allow us to only have settings not configurable in the fulfillment.
                    if ($setting->getSection() === static::METHOD_ONLY) {
                        return null;
                    }

                    return $setting->value($this->fulfillment->courier_configuration[$setting->key()]);
                })->filter();

                /**
                 * Must be a better way to do this, will need to speak with Aero.
                 * For now to get the settings to work on the fulfilment page we need a file.
                 */
                Settings::save($this->key, SettingHelpers::formatDataForSaving($this->settings, $this->fulfillment->courier_configuration->toArray()));
                Storage::delete("settings/{$this->key}.json");
            }
        }
    }

    /**
     * Check if the selected driver is a courier driver.
     */
    public function isValid(): bool
    {
        // We only have courier settings for courier drivers...
        if (method_exists($this->drivers[$this->courier], 'fulfillmentMethodSettings')) {
            return true;
        }

        return false;
    }

    /**
     * Generate a key for the selected method.
     */
    protected function generateKey(): string
    {
        if ($this->fulfillment) {
            return Str::slug("courier-fulfillment-{$this->fulfillment->id}");
        }

        if ($this->fulfillmentMethod instanceof FulfillmentMethod) {
            return Str::slug("courier-fulfillment-method-{$this->fulfillmentMethod->id}");
        }

        return Str::slug("courier-fulfillment-method-x");
    }

    /**
     * Validate the input with the current configuration.
     */
    public function validate()
    {
        if (!$this->isValid()) {
            return;
        }

        return Validator::make(
            SettingHelpers::formatDataForValidation($this->settings, request()->only($this->settings->keys()->toArray())),
            $this->settings->map->getRules()->merge($this->settings->map->getExtraRules()->collapse())->toArray(),
            [],
            $this->settings->mapWithKeys(function ($setting) {
                return [$this->group->key() => $setting->getLabel()];
            })->merge($this->settings->map->getExtraAttributes()->collapse())->toArray()
        );
    }

    /**
     * Save an Aero Settings file.
     */
    public function save(): void
    {
        if (!$this->isValid()) {
            return;
        }

        $validator = $this->validate();

        if ($this->fulfillment) {
            $this->fulfillment->courierConfiguration = $this->fulfillment->courierConfiguration->merge($validator->validated());
            $this->fulfillment->save();

            return;
        }

        Settings::save($this->key, SettingHelpers::formatDataForSaving($this->settings, $validator->validated()));
    }

    /**
     * Delete the Aero settings file for this configuration.
     */
    public function delete(): void
    {
        if (!$this->isValid()) {
            return;
        }

        if ($this->fulfillment) {
            dd('delete the model settings, not the file');

            return;
        }

        Storage::delete("settings/{$this->key}.json");
    }

    /**
     * Get the collection of settings.
     */
    public function settings(): Collection
    {
        return $this->settings;
    }

    /**
     * Get the setting group instance.
     */
    public function group(): SettingGroup
    {
        return $this->group;
    }

    /**
     * Get the the configuration key.
     */
    public function key(): string
    {
        return $this->key;
    }
}
