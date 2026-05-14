<?php

declare(strict_types=1);

namespace App\Services;

use App\Customer\CustomerContext;
use App\Repositories\MenuRepository;
use App\Support\Money;

/**
 * Menyiapkan data tampilan keranjang/checkout dari session + master menu.
 */
final class CartViewBuilder
{
    public function __construct(
        private readonly MenuRepository $menus,
        private readonly CartService $cart,
    ) {
    }

    /**
     * @return array{
     *   groups: list<array{warung_id:int, warung_name:string, items:list<array<string,mixed>>, subtotal:float}>,
     *   subtotal: float,
     *   itemCount: int,
     *   warungCount: int
     * }
     */
    public function summarize(CustomerContext $ctx): array
    {
        $lines = $this->cart->lines();
        if ($lines === []) {
            return ['groups' => [], 'subtotal' => 0.0, 'itemCount' => 0, 'warungCount' => 0];
        }

        $ids = array_map(static fn (array $l): int => $l['menu_id'], $lines);
        $map = $this->menus->mapByIds($ids);

        $bucket = [];
        $subtotal = 0.0;
        $itemCount = 0;

        foreach ($lines as $line) {
            $mid = $line['menu_id'];
            if (!isset($map[$mid])) {
                continue;
            }
            $m = $map[$mid];
            if ((int) $m['venue_id'] !== $ctx->venueId) {
                continue;
            }
            $wid = (int) $m['warung_id'];
            if (!isset($bucket[$wid])) {
                $bucket[$wid] = [
                    'warung_id' => $wid,
                    'warung_name' => (string) $m['warung_name'],
                    'items' => [],
                    'subtotal' => 0.0,
                ];
            }
            $unit = (float) $m['price'];
            $qty = $line['qty'];
            $lineSub = $unit * $qty;
            $bucket[$wid]['items'][] = [
                'menu_id' => $mid,
                'name' => (string) $m['name'],
                'qty' => $qty,
                'note' => $line['note'],
                'unit_price' => $unit,
                'line_subtotal' => $lineSub,
                'unit_price_label' => Money::formatIdr($unit),
                'line_subtotal_label' => Money::formatIdr($lineSub),
            ];
            $bucket[$wid]['subtotal'] += $lineSub;
            $subtotal += $lineSub;
            $itemCount += $qty;
        }

        $groups = array_values($bucket);
        foreach ($groups as &$g) {
            $g['subtotal_label'] = Money::formatIdr($g['subtotal']);
        }
        unset($g);

        return [
            'groups' => $groups,
            'subtotal' => $subtotal,
            'itemCount' => $itemCount,
            'warungCount' => count($groups),
        ];
    }
}
