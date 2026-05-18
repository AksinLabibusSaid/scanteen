<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\VenueRepository;

final class VenueOperatingService
{
    public function getStatus(int $venueId): array
    {
        $repo = new VenueRepository();
        $venue = $repo->findById($venueId);
        
        if (!$venue) {
            return ['isOpen' => false, 'reason' => 'Venue not found'];
        }

        if ((int)($venue['maintenance_mode'] ?? 0) === 1) {
            return ['isOpen' => false, 'reason' => 'maintenance', 'message' => $venue['maintenance_message'] ?? 'Sedang dalam pemeliharaan.'];
        }

        $oh = json_decode($venue['operating_hours'] ?? '{}', true);
        if (empty($oh)) {
            return ['isOpen' => true, 'reason' => 'no_config'];
        }

        // Timezone check - assuming Asia/Jakarta as default if not specified
        date_default_timezone_set('Asia/Jakarta');
        $now = new \DateTime();
        $dayOfWeek = (int)$now->format('w'); // 0 (Sun) - 6 (Sat)
        $currentTime = $now->format('H:i');

        $schedule = null;
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
            $schedule = $oh['mon_fri'] ?? null;
        } elseif ($dayOfWeek === 6) {
            $schedule = $oh['sat'] ?? null;
        } elseif ($dayOfWeek === 0) {
            $schedule = $oh['sun'] ?? null;
        }

        if (!$schedule || !($schedule['active'] ?? false)) {
            return ['isOpen' => false, 'reason' => 'closed_today'];
        }

        $openTime = $schedule['open'];
        $closeTime = $schedule['close'];

        if ($currentTime < $openTime || $currentTime > $closeTime) {
            return ['isOpen' => false, 'reason' => 'outside_hours', 'open' => $openTime, 'close' => $closeTime];
        }

        return ['isOpen' => true, 'reason' => 'operating'];
    }
}
