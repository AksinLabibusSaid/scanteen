<?php

declare(strict_types=1);

namespace App\Customer;

final class CustomerSessionKeys
{
    public const TABLE_ID = 'scanteen_customer_table_id';
    public const TABLE_NUMBER = 'scanteen_customer_table_number';
    public const VENUE_ID = 'scanteen_customer_venue_id';
    public const VENUE_NAME = 'scanteen_customer_venue_name';
    public const BARCODE_TOKEN = 'scanteen_customer_barcode_token';
    public const CART = 'scanteen_customer_cart';
    public const CHECKOUT_DRAFT = 'scanteen_customer_checkout_draft';
    public const LAST_ORDER_TOKEN = 'scanteen_customer_last_order_token';
}
