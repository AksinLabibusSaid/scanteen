<?php

declare(strict_types=1);

namespace App\Customer;

/**
 * Immutable snapshot of the customer session (meja + venue).
 */
final class CustomerContext
{
    public function __construct(
        public readonly int $diningTableId,
        public readonly string $tableNumber,
        public readonly int $venueId,
        public readonly string $venueName,
        public readonly string $barcodeToken,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            diningTableId: (int) $row['dining_table_id'],
            tableNumber: (string) $row['table_number'],
            venueId: (int) $row['venue_id'],
            venueName: (string) $row['venue_name'],
            barcodeToken: (string) $row['barcode_token'],
        );
    }
}
