<?php

namespace Techquity\Aero\Couriers;

use Aero\Admin\Utils\SettingHelpers;
use Aero\Common\Settings\SettingGroup;
use Illuminate\Support\Collection;
use Aero\Fulfillment\FulfillmentProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class AbstractCourierConfiguration
{
    /**
     * The settings group key.
     */
    protected string $key;

    /**
     * The current selected fulfillment driver.
     */
    protected string $driver;

    /**
     * Array of available drivers.
     */
    protected array $drivers;

    /**
     * The setting group instance.
     */
    protected SettingGroup $group;

    /**
     * Collection of settings.
     */
    protected Collection $settings;

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

    /**
     * Set the drivers and selected driver.
     */
    protected function configureDrivers(string $driver): void
    {
        $this->drivers = FulfillmentProcessor::getDrivers();

        // If no driver exists try and get it from the method...
        $this->driver = $this->drivers[$driver ?? $this->fulfillmentMethod->driver];
    }

    /**
     * Check if the current driver is a courier.
     */
    public function hasCourierConfiguration(): bool
    {
        return method_exists($this->driver, 'fulfillmentMethodSettings');
    }

    /**
     * Validate the input with the current configuration.
     */
    public function validator(Request $request)
    {
        if (!$this->hasCourierConfiguration()) {
            return;
        }

        return Validator::make(
            SettingHelpers::formatDataForValidation($this->settings, $request->only($this->settings->keys()->toArray())),
            $this->settings->map->getRules()->merge($this->settings->map->getExtraRules()->collapse())->toArray(),
            [],
            $this->settings->mapWithKeys(function ($setting) {
                return [$this->group->key() => $setting->getLabel()];
            })->merge($this->settings->map->getExtraAttributes()->collapse())->toArray()
        );
    }
}
