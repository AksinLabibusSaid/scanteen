<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Repositories\DiningTableRepository;
use App\Support\PublicUrl;
use chillerlan\QRCode\QRCode;

$dtr = new DiningTableRepository();
$allTables = $dtr->listByVenueId(1);
$qrGenerator = new QRCode();

foreach ($allTables as $t) {
    $scanUrl = PublicUrl::customerScanUrl($t['barcode_token']);
    $dataUri = $qrGenerator->render($scanUrl);
    echo "Meja " . $t['table_number'] . " URI Length: " . strlen($dataUri) . "\n";
    echo "Hash: " . md5($dataUri) . "\n";
}
