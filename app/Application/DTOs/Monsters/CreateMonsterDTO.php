<?php

namespace App\Application\DTOs\Monsters;

use App\Core\Exceptions\ValidationException;

class CreateMonsterDTO
{
    public string $name;
    public ?string $description;

    public int $base_hp;
    public int $base_ac;
    public int $base_speed;

    public int $actions_per_turn;

    public int $base_str;
    public int $base_dex;
    public int $base_con;
    public int $base_wis;
    public int $base_int;

    public array $element_types;

    public function __construct(array $data)
    {
        $this->name = trim($data['name'] ?? '');

        if ($this->name === '') {
            throw new ValidationException('Nome do monstro é obrigatório.');
        }

        $this->description = $data['description'] ?? null;

        $this->base_hp = (int) ($data['base_hp'] ?? 1);
        $this->base_ac = (int) ($data['base_ac'] ?? 10);
        $this->base_speed = (int) ($data['base_speed'] ?? 6);

        $this->actions_per_turn = (int) ($data['actions_per_turn'] ?? 3);

        $this->base_str = (int) ($data['base_str'] ?? 1);
        $this->base_dex = (int) ($data['base_dex'] ?? 1);
        $this->base_con = (int) ($data['base_con'] ?? 1);
        $this->base_wis = (int) ($data['base_wis'] ?? 1);
        $this->base_int = (int) ($data['base_int'] ?? 1);

        $this->element_types = $data['element_types'] ?? [];

        if (!is_array($this->element_types)) {
            throw new ValidationException('Tipos elementais devem ser um array.');
        }
    }
}
