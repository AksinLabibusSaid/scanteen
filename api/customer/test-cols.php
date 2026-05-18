<?php
require __DIR__ . '/../../app/bootstrap.php';
require __DIR__ . '/../../config/db.php';
$m = \App\Core\Database::mysqli();
$r = $m->query("ALTER TABLE staff_users ADD COLUMN phone varchar(20) DEFAULT NULL AFTER name");
echo "Result: " . ($r ? "Success" : "Failed: " . $m->error);
