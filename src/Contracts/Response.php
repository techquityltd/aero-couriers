<?php

namespace Techquity\Aero\Couriers\Contracts;

interface Response
{
    /**
     * Get the original array response data.
     */
    public function original(): array;

    /**
     * Get the array response data.
     */
    public function array(): array;

    /**
     * Get the failed error messages to be logged.
     */
    public function getFailedMessages(): array;

    /**
     * Get the reference from the shipment.
     */
    public function getConsignmentNumber(): string;

    /**
     * Get the shipments tracking number.
     */
    public function getTrackingNumber(): string;

    /**
     * Get the the tracking url.
     */
    public function getTrackingUrl(): string;

    /**
     * Get the label file type.
     */
    public function getLabelFileType(): string;

    /**
     * Get the decoded label.
     */
    public function getLabel(): string;
}
