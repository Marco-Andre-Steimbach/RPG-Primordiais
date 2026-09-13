<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class AbilityNewRepository extends BaseRepository
{
    protected string $table = 'abilities_new';

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return [
            'id' => (int) $row['id'],
            'required_race_id' => $row['required_race_id'] !== null
                ? (int) $row['required_race_id']
                : null,
            'required_order_id' => $row['required_order_id'] !== null
                ? (int) $row['required_order_id']
                : null,
            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
        ];
    }

    public function findCompleteById(int $id): ?array
    {
        $ability = $this->findById($id);

        if (!$ability) {
            return null;
        }

        $formRepo = new AbilityNewFormRepository();
        $ruleNodeRepo = new AbilityNewRuleNodeRepository();

        $forms = $formRepo->findByAbilityId($id);

        $mappedForms = [];

        foreach ($forms as $form) {
            $form['rules'] = $ruleNodeRepo->getTreeByFormId(
                (int) $form['id']
            );

            $mappedForms[$form['form_type']] = $form;
        }

        $ability['forms'] = $mappedForms;

        return $ability;
    }

    public function exists(int $id): bool
    {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return (bool) $stmt->fetchColumn();
    }
}
