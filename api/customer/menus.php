<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\MenuRepository;

/** @var \App\Customer\CustomerContext $ctx */
[$ctx, $_c, $_m, $_b] = scanteen_customer_api_bootstrap();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    scanteen_json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$rows = (new MenuRepository())->listByVenue($ctx->venueId);
$warungs = [];
$categories = [];
foreach ($rows as $r) {
    $wid = (int) $r['warung_id'];
    $warungs[$wid] = (string) $r['warung_name'];
    $catName = (string) $r['category_name'];
    if ((int) $r['category_id'] !== 1) {
        $categories[(int) $r['category_id']] = $catName;
    }
}

scanteen_json_response([
    'ok' => true,
    'table' => [
        'number' => $ctx->tableNumber,
        'venue' => $ctx->venueName,
    ],
    'menus' => array_map(static function (array $r): array {
        return [
            'id' => (int) $r['id'],
            'name' => (string) $r['name'],
            'price' => (float) $r['price'],
            'stock' => (int) ($r['stock_quantity'] ?? 0),
            'is_available' => (int) ($r['is_available'] ?? 0),
            'image' => (string) ($r['image_url'] ?? ''),
            'warung' => (string) $r['warung_name'],
            'warung_id' => (int) $r['warung_id'],
            'category' => (string) $r['category_name'],
            'category_slug' => (string) $r['category_slug'],
            'is_available' => (int) $r['is_available'],
        ];
    }, $rows),
    'warung_tabs' => array_merge(['Semua'], array_values(array_unique(array_values($warungs)))),
    'category_tabs' => array_merge(['Semua'], array_values(array_unique(array_values($categories)))),
]);
