<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class MenuRepository
{
    /**
     * @return list<array<string, mixed>>
     */
    public function listByVenue(int $venueId): array
    {
        $sql = <<<SQL
            SELECT
                m.id,
                m.name,
                m.description,
                m.price,
                m.stock_quantity,
                m.image_url,
                m.is_available,
                w.id AS warung_id,
                w.name AS warung_name,
                c.id AS category_id,
                c.name AS category_name,
                c.slug AS category_slug
            FROM menus m
            INNER JOIN warungs w ON w.id = m.warung_id AND w.is_active = 1
            INNER JOIN menu_categories c ON c.id = m.category_id
            WHERE w.venue_id = ?
            ORDER BY w.sort_order ASC, m.id ASC
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $venueId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * @param list<int> $ids
     * @return array<int, array<string, mixed>> keyed by menu id
     */
    public function mapByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter($ids, static fn ($v) => is_int($v) || ctype_digit((string) $v))));
        if ($ids === []) {
            return [];
        }

        $ints = array_map(static fn ($v) => (int) $v, $ids);
        $placeholders = implode(',', array_fill(0, count($ints), '?'));
        $types = str_repeat('i', count($ints));

        $sql = <<<SQL
            SELECT
                m.id,
                m.name,
                m.price,
                m.stock_quantity,
                m.is_available,
                w.id AS warung_id,
                w.name AS warung_name,
                w.venue_id
            FROM menus m
            INNER JOIN warungs w ON w.id = m.warung_id
            WHERE m.id IN ($placeholders)
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param($types, ...$ints);
        $stmt->execute();
        $result = $stmt->get_result();
        $map = [];
        while ($row = $result->fetch_assoc()) {
            $map[(int) $row['id']] = $row;
        }
        $stmt->close();

        return $map;
    }

    /**
     * Semua menu venue (termasuk tidak tersedia) untuk admin / warung.
     *
     * @return list<array<string, mixed>>
     */
    public function listAdminByVenue(int $venueId, ?int $warungId = null, ?int $categoryId = null): array
    {
        $sql = <<<SQL
            SELECT
                m.id,
                m.name,
                m.description,
                m.price,
                m.stock_quantity,
                m.image_url,
                m.is_available,
                m.warung_id,
                w.name AS warung_name,
                c.id AS category_id,
                c.name AS category_name
            FROM menus m
            INNER JOIN warungs w ON w.id = m.warung_id
            INNER JOIN menu_categories c ON c.id = m.category_id
            WHERE w.venue_id = ?
            SQL;
        if ($warungId !== null && $warungId > 0) {
            $sql .= ' AND m.warung_id = ?';
        }
        if ($categoryId !== null && $categoryId > 0) {
            $sql .= ' AND m.category_id = ?';
        }
        $sql .= ' ORDER BY w.sort_order ASC, m.id ASC';

        $mysqli = Database::mysqli();
        $stmt = $mysqli->prepare($sql);
        
        if ($warungId !== null && $warungId > 0 && $categoryId !== null && $categoryId > 0) {
            $stmt->bind_param('iii', $venueId, $warungId, $categoryId);
        } elseif ($warungId !== null && $warungId > 0) {
            $stmt->bind_param('ii', $venueId, $warungId);
        } elseif ($categoryId !== null && $categoryId > 0) {
            $stmt->bind_param('ii', $venueId, $categoryId);
        } else {
            $stmt->bind_param('i', $venueId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    public function menuBelongsToVenue(int $menuId, int $venueId): bool
    {
        $sql = <<<SQL
            SELECT 1 FROM menus m
            INNER JOIN warungs w ON w.id = m.warung_id
            WHERE m.id = ? AND w.venue_id = ?
            LIMIT 1
            SQL;
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('ii', $menuId, $venueId);
        $stmt->execute();
        $ok = $stmt->get_result()->fetch_row() !== null;
        $stmt->close();

        return $ok;
    }

    public function menuBelongsToWarung(int $menuId, int $warungId): bool
    {
        $sql = 'SELECT 1 FROM menus WHERE id = ? AND warung_id = ? LIMIT 1';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('ii', $menuId, $warungId);
        $stmt->execute();
        $ok = $stmt->get_result()->fetch_row() !== null;
        $stmt->close();

        return $ok;
    }

    public function setAvailability(int $menuId, int $isAvailable): bool
    {
        $sql = 'UPDATE menus SET is_available = ? WHERE id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('ii', $isAvailable, $menuId);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();

        return $ok;
    }

    public function insertMenu(
        int $warungId,
        int $categoryId,
        string $name,
        ?string $description,
        float $price,
        ?string $imageUrl,
        int $isAvailable,
    ): int {
        $name = trim($name);
        $description = $description !== null && trim($description) !== '' ? trim($description) : '';
        $imageUrl = $imageUrl !== null && trim($imageUrl) !== '' ? trim($imageUrl) : '';
        $sql = <<<SQL
            INSERT INTO menus (warung_id, category_id, name, description, price, image_url, is_available)
            VALUES (?,?,?,?,?,?,?)
            SQL;
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('iissdsi', $warungId, $categoryId, $name, $description, $price, $imageUrl, $isAvailable);
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function updateMenu(
        int $menuId,
        int $warungId,
        int $categoryId,
        string $name,
        ?string $description,
        float $price,
        ?string $imageUrl,
        int $isAvailable,
    ): bool {
        $name = trim($name);
        $description = $description !== null && trim($description) !== '' ? trim($description) : '';
        $imageUrl = $imageUrl !== null && trim($imageUrl) !== '' ? trim($imageUrl) : '';
        $sql = <<<SQL
            UPDATE menus SET
                category_id = ?, name = ?, description = ?, price = ?, image_url = ?, is_available = ?
            WHERE id = ? AND warung_id = ?
            SQL;
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('issdsiii', $categoryId, $name, $description, $price, $imageUrl, $isAvailable, $menuId, $warungId);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();

        return $ok;
    }

    public function deleteMenu(int $menuId, int $warungId): bool
    {
        $sql = 'DELETE FROM menus WHERE id = ? AND warung_id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('ii', $menuId, $warungId);
        $stmt->execute();
        $ok = $stmt->affected_rows === 1;
        $stmt->close();

        return $ok;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listCategories(): array
    {
        $sql = 'SELECT id, name, slug FROM menu_categories ORDER BY sort_order ASC, id ASC';
        $res = Database::mysqli()->query($sql);
        $rows = [];
        if ($res !== false) {
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }
        }

        return $rows;
    }
    public function updateStock(int $menuId, int $warungId, int $newStock): bool
    {
        $isAvailable = $newStock > 0 ? 1 : 0;
        $sql = 'UPDATE menus SET stock_quantity = ?, is_available = ? WHERE id = ? AND warung_id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('iiii', $newStock, $isAvailable, $menuId, $warungId);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();

        return $ok;
    }

    public function syncAvailabilityWithStock(int $warungId): void
    {
        $sql = 'UPDATE menus SET is_available = 0 WHERE warung_id = ? AND stock_quantity = 0';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $warungId);
        $stmt->execute();
        $stmt->close();
    }
}
