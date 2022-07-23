<?php

namespace Techquity\Aero\Couriers;

use Aero\Admin\Utils\SettingHelpers;
use Aero\Common\Facades\Settings;
use Aero\Common\Settings\SettingGroup;
use Aero\Fulfillment\FulfillmentProcessor;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourierConfiguration
{
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
        }
    }

    protected function attachFulfillmentSettings()
    {
        // useing aero settings add the customised values if any
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
            //$this->fulfillment->courierConfiguration
            dd($validator->validated());
            dd('save to the model, not the file');

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
