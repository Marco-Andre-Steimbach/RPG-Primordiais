<?php

namespace App\Domain\Services\Campaigns;

use App\Application\DTOs\Campaigns\AddItemToCampaignCharacterDTO;
use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignCharacterItemRepository;
use App\Infrastructure\Repositories\ItemRepository;
use App\Infrastructure\Repositories\WeaponRepository;
use App\Infrastructure\Repositories\ArmorRepository;

class AddItemToCampaignCharacterService
{
    private CampaignCharacterRepository $campaignCharacters;
    private CampaignCharacterItemRepository $campaignItems;
    private ItemRepository $items;
    private WeaponRepository $weapons;
    private ArmorRepository $armors;

    public function __construct()
    {
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->campaignItems      = new CampaignCharacterItemRepository();
        $this->items              = new ItemRepository();
        $this->weapons            = new WeaponRepository();
        $this->armors             = new ArmorRepository();
    }

    public function execute(
        int $campaignCharacterId,
        AddItemToCampaignCharacterDTO $dto
    ): void {
        $campaignCharacter = $this->campaignCharacters->findById($campaignCharacterId);

        if (!$campaignCharacter) {
            throw new ValidationException(
                'Personagem inválido.',
                ['campaign_character_id' => ['Não encontrado.']]
            );
        }

        $item = $this->items->findById($dto->item_id);

        if (!$item) {
            throw new ValidationException(
                'Item inválido.',
                ['item_id' => ['Item não encontrado.']]
            );
        }

        if ($this->weapons->existsByItemId($dto->item_id)) {
            throw new ValidationException(
                'Item inválido.',
                ['item_id' => ['Este item é uma arma e deve ser adicionada pela rota de armas.']]
            );
        }

        if ($this->armors->existsByItemId($dto->item_id)) {
            throw new ValidationException(
                'Item inválido.',
                ['item_id' => ['Este item é uma armadura e deve ser adicionada pela rota de armaduras.']]
            );
        }

        $existingItem = $this->campaignItems->findByCampaignCharacterAndItem(
            $campaignCharacterId,
            $dto->item_id
        );

        if ($existingItem) {
            $this->campaignItems->increaseQuantity(
                $existingItem['id'],
                $dto->quantity
            );
            return;
        }

        $this->campaignItems->create([
            'campaign_character_id' => $campaignCharacterId,
            'item_id'               => $dto->item_id,
            'quantity'              => $dto->quantity,
            'is_active'   => 1,
            'is_equipped' => 0,
        ]);
    }
}
