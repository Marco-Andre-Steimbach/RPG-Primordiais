<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\Perk;
use PDO;

class PerkRepository extends BaseRepository
{
    protected string $table = 'perks';

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO {$this->table}
            (
                name,
                description,
                type,
                mana_cost
            )
            VALUES
            (
                :name,
                :description,
                :type,
                :mana_cost
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'type' => $data['type'],
            'mana_cost' => $data['mana_cost'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?Perk
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id' => $id,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Perk(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description'],
            type: $row['type'],
            mana_cost: (int) $row['mana_cost'],

            race_id: null,
            order_id: null,
            required_level: 1,

            element_types: [],
            flags: [],
            attributes: [],
            ability: null,

            required_perk_ids:
                $this->getRequiredPerkIds(
                    (int) $row['id']
                ),

            sheet_perk_ids:
                $this->getSheetPerkIds(
                    (int) $row['id']
                ),

            sheet:
                $this->getSheetByPerkId(
                    (int) $row['id']
                ),

            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null
        );
    }

    public function existsById(int $id): bool
    {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id' => $id,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function existsByName(string $name): bool
    {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE name = :name
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'name' => $name,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function attachRequirement(
        int $perkId,
        int $requiredPerkId
    ): bool {
        $sql = "
            INSERT INTO perk_requirements
            (
                perk_id,
                required_perk_id
            )
            VALUES
            (
                :perk_id,
                :required_perk_id
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'perk_id' => $perkId,
            'required_perk_id' => $requiredPerkId,
        ]);
    }

    public function getRequiredPerkIds(
        int $perkId
    ): array {
        $sql = "
            SELECT required_perk_id
            FROM perk_requirements
            WHERE perk_id = :perk_id
            ORDER BY id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'perk_id' => $perkId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return array_map(
            'intval',
            $rows ?: []
        );
    }

    public function createSheet(
        int $perkId,
        array $sheet
    ): int {
        $sql = "
            INSERT INTO perk_sheets
            (
                perk_id,
                sheet_type,
                entity_mode,

                hp_source,
                base_hp,
                owner_hp_numerator,
                owner_hp_denominator,

                base_mana,
                owner_mana_numerator,
                owner_mana_denominator,
                mana_mode,
                mana_cost_multiplier,

                armor_class,
                armor_class_mode,

                speed,
                speed_mode,

                actions_per_turn,
                actions_mode,

                initiative_rule,
                initiative_mode,

                duration_formula,
                duration_unit,
                hp_revert_mode,

                auto_spawn,
                restore_hp_on_spawn,
                restore_mana_on_spawn
            )
            VALUES
            (
                :perk_id,
                :sheet_type,
                :entity_mode,

                :hp_source,
                :base_hp,
                :owner_hp_numerator,
                :owner_hp_denominator,

                :base_mana,
                :owner_mana_numerator,
                :owner_mana_denominator,
                :mana_mode,
                :mana_cost_multiplier,

                :armor_class,
                :armor_class_mode,

                :speed,
                :speed_mode,

                :actions_per_turn,
                :actions_mode,

                :initiative_rule,
                :initiative_mode,

                :duration_formula,
                :duration_unit,
                :hp_revert_mode,

                :auto_spawn,
                :restore_hp_on_spawn,
                :restore_mana_on_spawn
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'perk_id' =>
                $perkId,

            'sheet_type' =>
                $sheet['sheet_type'],

            'entity_mode' =>
                $sheet['entity_mode'],

            'hp_source' =>
                $sheet['hp_source'],

            'base_hp' =>
                $sheet['base_hp'],

            'owner_hp_numerator' =>
                $sheet['owner_hp_numerator'],

            'owner_hp_denominator' =>
                $sheet['owner_hp_denominator'],

            'base_mana' =>
                $sheet['base_mana'],

            'owner_mana_numerator' =>
                $sheet['owner_mana_numerator'],

            'owner_mana_denominator' =>
                $sheet['owner_mana_denominator'],

            'mana_mode' =>
                $sheet['mana_mode'],

            'mana_cost_multiplier' =>
                $sheet['mana_cost_multiplier'],

            'armor_class' =>
                $sheet['armor_class'],

            'armor_class_mode' =>
                $sheet['armor_class_mode'],

            'speed' =>
                $sheet['speed'],

            'speed_mode' =>
                $sheet['speed_mode'],

            'actions_per_turn' =>
                $sheet['actions_per_turn'],

            'actions_mode' =>
                $sheet['actions_mode'],

            'initiative_rule' =>
                $sheet['initiative_rule'],

            'initiative_mode' =>
                $sheet['initiative_mode'],

            'duration_formula' =>
                $sheet['duration_formula'],

            'duration_unit' =>
                $sheet['duration_unit'],

            'hp_revert_mode' =>
                $sheet['hp_revert_mode'],

            'auto_spawn' =>
                $sheet['auto_spawn'] ? 1 : 0,

            'restore_hp_on_spawn' =>
                $sheet['restore_hp_on_spawn'] ? 1 : 0,

            'restore_mana_on_spawn' =>
                $sheet['restore_mana_on_spawn'] ? 1 : 0,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getSheetByPerkId(
        int $perkId
    ): ?array {
        $sql = "
            SELECT
                id,
                perk_id,
                sheet_type,
                entity_mode,

                hp_source,
                base_hp,
                owner_hp_numerator,
                owner_hp_denominator,

                base_mana,
                owner_mana_numerator,
                owner_mana_denominator,
                mana_mode,
                mana_cost_multiplier,

                armor_class,
                armor_class_mode,

                speed,
                speed_mode,

                actions_per_turn,
                actions_mode,

                initiative_rule,
                initiative_mode,

                duration_formula,
                duration_unit,
                hp_revert_mode,

                auto_spawn,
                restore_hp_on_spawn,
                restore_mana_on_spawn,

                created_at,
                updated_at

            FROM perk_sheets

            WHERE perk_id = :perk_id

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'perk_id' => $perkId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return [
            'id' =>
                (int) $row['id'],

            'perk_id' =>
                (int) $row['perk_id'],

            'sheet_type' =>
                $row['sheet_type'],

            'entity_mode' =>
                $row['entity_mode'],

            'hp_source' =>
                $row['hp_source'],

            'base_hp' =>
                (int) $row['base_hp'],

            'owner_hp_numerator' =>
                (int) $row['owner_hp_numerator'],

            'owner_hp_denominator' =>
                (int) $row['owner_hp_denominator'],

            'base_mana' =>
                (int) $row['base_mana'],

            'owner_mana_numerator' =>
                (int) $row['owner_mana_numerator'],

            'owner_mana_denominator' =>
                (int) $row['owner_mana_denominator'],

            'mana_mode' =>
                $row['mana_mode'],

            'mana_cost_multiplier' =>
                (float) $row['mana_cost_multiplier'],

            'armor_class' =>
                (int) $row['armor_class'],

            'armor_class_mode' =>
                $row['armor_class_mode'],

            'speed' =>
                (int) $row['speed'],

            'speed_mode' =>
                $row['speed_mode'],

            'actions_per_turn' =>
                (int) $row['actions_per_turn'],

            'actions_mode' =>
                $row['actions_mode'],

            'initiative_rule' =>
                $row['initiative_rule'],

            'initiative_mode' =>
                $row['initiative_mode'],

            'duration_formula' =>
                $row['duration_formula'],

            'duration_unit' =>
                $row['duration_unit'],

            'hp_revert_mode' =>
                $row['hp_revert_mode'],

            'auto_spawn' =>
                (bool) $row['auto_spawn'],

            'restore_hp_on_spawn' =>
                (bool) $row['restore_hp_on_spawn'],

            'restore_mana_on_spawn' =>
                (bool) $row['restore_mana_on_spawn'],

            'created_at' =>
                $row['created_at'] ?? null,

            'updated_at' =>
                $row['updated_at'] ?? null,
        ];
    }

    public function sheetExistsByPerkId(
        int $perkId
    ): bool {
        $sql = "
            SELECT 1
            FROM perk_sheets
            WHERE perk_id = :perk_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'perk_id' => $perkId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function attachToSheet(
        int $sheetPerkId,
        int $perkId,
        ?int $displayOrder = null
    ): bool {
        $sheetId = $this->getSheetIdByPerkId(
            $sheetPerkId
        );

        if ($sheetId === null) {
            return false;
        }

        $sql = "
            INSERT INTO perk_sheet_perks
            (
                perk_sheet_id,
                perk_id,
                display_order
            )
            VALUES
            (
                :perk_sheet_id,
                :perk_id,
                :display_order
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'perk_sheet_id' => $sheetId,
            'perk_id' => $perkId,
            'display_order' => $displayOrder,
        ]);
    }

    public function getSheetPerkIds(
        int $perkId
    ): array {
        $sql = "
            SELECT ps.perk_id
            FROM perk_sheet_perks psp

            INNER JOIN perk_sheets ps
                ON ps.id = psp.perk_sheet_id

            WHERE psp.perk_id = :perk_id

            ORDER BY
                psp.display_order IS NULL,
                psp.display_order,
                psp.id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'perk_id' => $perkId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return array_map(
            'intval',
            $rows ?: []
        );
    }

    private function getSheetIdByPerkId(
        int $perkId
    ): ?int {
        $sql = "
            SELECT id
            FROM perk_sheets
            WHERE perk_id = :perk_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'perk_id' => $perkId,
        ]);

        $id = $stmt->fetchColumn();

        if ($id === false) {
            return null;
        }

        return (int) $id;
    }
}
