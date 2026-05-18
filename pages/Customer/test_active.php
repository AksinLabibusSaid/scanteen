<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
session_start();

use App\Customer\CustomerAccess;
use App\Customer\CustomerSessionKeys;

$_GET['t'] = 'TBL-6422da69'; // table 2 active token
CustomerAccess::syncTableTokenFromRequest();

$context = CustomerAccess::contextFromSession();
echo "Table Number: " . ($context ? $context->tableNumber : 'null') . "\n";
