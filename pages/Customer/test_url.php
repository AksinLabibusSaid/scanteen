<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Repositories\DiningTableRepository;
use App\Support\PublicUrl;

$dtr = new DiningTableRepository();
$allTables = $dtr->listByVenueId(1);

foreach ($allTables as $t) {
    echo "Meja " . $t['table_number'] . ": " . PublicUrl::customerScanUrl($t['barcode_token']) . "\n";
}
