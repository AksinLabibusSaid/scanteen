<?php
require 'config/db.php';
use App\Repositories\DiningTableWriteRepository;
use App\Core\Database;

$repo = new DiningTableWriteRepository();
$venueId = 1; // Assuming venue_id is 1

// Let's find a dining table ID to test delete on
$mysqli = Database::mysqli();
$res = $mysqli->query("SELECT id, table_number, is_active FROM dining_tables WHERE venue_id = $venueId LIMIT 5");
echo "=== DINING TABLES ===\n";
$tables = [];
while ($row = $res->fetch_assoc()) {
    $tables[] = $row;
    print_r($row);
}

if (!empty($tables)) {
    $target = $tables[0];
    $id = (int)$target['id'];
    echo "\nTrying to delete Table ID: $id (Number: {$target['table_number']})\n";
    try {
        $ok = $repo->delete($id, $venueId);
        echo "Hard delete result: " . ($ok ? "SUCCESS" : "FAILED (0 rows affected)") . "\n";
    } catch (\Throwable $e) {
        echo "Hard delete threw exception: " . $e->getMessage() . "\n";
        echo "Falling back to soft delete...\n";
        try {
            $ok = $repo->softDelete($id, $venueId);
            echo "Soft delete result: " . ($ok ? "SUCCESS" : "FAILED") . "\n";
        } catch (\Throwable $ex) {
            echo "Soft delete threw exception: " . $ex->getMessage() . "\n";
        }
    }
}
