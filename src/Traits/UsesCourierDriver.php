<?php

namespace Techquity\Aero\Couriers\Traits;

use Aero\Common\Requests\AeroRequest;
use Aero\Responses\ResponseBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;
use Techquity\Aero\Couriers\CourierDriver;
use Techquity\Aero\Couriers\Models\CourierConnector;
use Techquity\Aero\Couriers\Models\CourierPrinter;
use Techquity\Aero\Couriers\Models\CourierService;

trait UsesCourierDriver
{
    public static $selector_view = 'couriers::slots.fulfillments.service-selector';

    public static function extendRequestForSelector($class)
    {
        if (!is_subclass_of($class, AeroRequest::class)) {
            throw new \RuntimeException('Courier request must be an instance of Aero\Common\Requests\AeroRequest');
        }

        $class::expects('service', static::courierServiceRules());
        $class::expects('connector', static::courierConnectionRules());
        $class::expects('printer', static::courierPrinterRules());
    }

    public static function getCourierDrivers()
    {
        return collect(Relation::$morphMap)->filter(function ($relation) {
            return is_subclass_of($relation, CourierDriver::class);
        });
    }

    public static function attachCourierMethods(ResponseBuilder $builder): void
    {
        if (!$builder->getData('methods')) {
            return;
        }

        $builder->setData('courierMethods', $builder->getData('methods')
            ->filter(fn ($method) => $method->isCourier)
            ->mapWithKeys(fn ($method) => [
                $method->id => [
                    'driver' => $method->driver,
                    'service' => optional($method->courierService)->id,
                    'printer' => optional($method->courierPrinter)->id,
                    'connector' => optional($method->courierConnector)->id
                ]
            ]));
    }

    public static function attachCourierDrivers(ResponseBuilder $builder): void
    {
        $builder->setData('courierDrivers', static::getCourierDrivers()->keys()->toArray());
    }

    protected static function attachCourierOptionsData(ResponseBuilder $builder)
    {
        $builder->setData('services', CourierService::displayAvailable());
        $builder->setData('connectors', CourierConnector::displayAvailable());
        $builder->setData('printers', CourierPrinter::displayAvailable());

        // Fulfillment Method selections...
        if ($fulfillmentMethod = $builder->getData('fulfillmentMethod')) {
            $selectedService = optional($fulfillmentMethod->courierService)->id;
            $selectedConnector = optional($fulfillmentMethod->courierConnector)->id;
            $selectedPrinter = optional($fulfillmentMethod->courierPrinter)->id;
        }

        // Fulfillment selections...
        if ($fulfillment = $builder->getData('fulfillment')) {
            $selectedService = optional($fulfillment->courierShipment->courierService)->id;
            $selectedConnector = optional($fulfillment->courierShipment->courierConnector)->id;
            $selectedPrinter = optional($fulfillment->courierShipment->courierPrinter)->id;
        }

        $builder->setData('selectedService', (int) old('service', $selectedService ?? null));
        $builder->setData('selectedConnector', (int) old('connector', $selectedConnector ?? null));
        $builder->setData('selectedPrinter', (int) old('printer', $selectedPrinter ?? null));
    }

    protected static function courierServiceRules(): array
    {
        return [
            Rule::requiredIf(fn () => static::getCourierDrivers()->has(request('driver'))),
            Rule::exists('courier_services', 'id'),
        ];
    }
    protected static function courierConnectionRules(): array
    {
        return [
            Rule::requiredIf(fn () => static::getCourierDrivers()->has(request('driver'))),
            Rule::exists('courier_connectors', 'id'),
        ];
    }
    protected static function courierPrinterRules(): array
    {
        return [
            'nullable',
            Rule::exists('courier_printers', 'id'),
        ];
    }
}
