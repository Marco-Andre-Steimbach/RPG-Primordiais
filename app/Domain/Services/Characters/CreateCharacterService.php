<?php

namespace App\Domain\Services\Characters;

use App\Application\DTOs\Characters\CreateCharacterDTO;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\Character;
use App\Infrastructure\Repositories\CharacterRepository;
use App\Infrastructure\Repositories\RaceRepository;
use App\Infrastructure\Repositories\OrderRepository;
use App\Infrastructure\Repositories\UserCharacterRepository;

class CreateCharacterService
{
    private CharacterRepository $characters;
    private RaceRepository $races;
    private OrderRepository $orders;
    private UserCharacterRepository $userCharacters;

    public function __construct()
    {
        $this->characters = new CharacterRepository();
        $this->races = new RaceRepository();
        $this->orders = new OrderRepository();
        $this->userCharacters = new UserCharacterRepository();
    }

    public function execute(CreateCharacterDTO $dto, ?int $userId = null): Character
    {
        if ($dto->race_id !== null && !$this->races->findById($dto->race_id)) {
            throw new ValidationException(
                'Raça inválida.',
                ['race_id' => ['Raça não encontrada.']]
            );
        }

        if ($dto->order_id !== null && !$this->orders->findById($dto->order_id)) {
            throw new ValidationException(
                'Ordem inválida.',
                ['order_id' => ['Ordem não encontrada.']]
            );
        }

        $characterId = $this->characters->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'race_id' => $dto->race_id,
            'order_id' => $dto->order_id,
            'mana_modifier' => $dto->mana_modifier,
            'created_by' => $userId,
        ]);

        if (!$characterId) {
            throw new ValidationException('Falha ao criar personagem.');
        }

        if ($userId !== null) {
            $this->userCharacters->attach($userId, $characterId);
        }

        $character = $this->characters->findById($characterId);

        if (!$character) {
            throw new ValidationException('Erro ao carregar personagem criado.');
        }

        return $character;
    }
}
