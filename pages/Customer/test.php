<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
session_start();

use App\Customer\CustomerAccess;
use App\Customer\CustomerSessionKeys;

$_GET['t'] = 'TBL-de027087'; // table 2
CustomerAccess::syncTableTokenFromRequest();

$context = CustomerAccess::contextFromSession();
echo "Table Number: " . ($context ? $context->tableNumber : 'null') . "\n";
