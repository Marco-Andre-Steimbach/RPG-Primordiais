<?php

namespace App\Domain\Services\Campaigns;

use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CampaignCharacterItemRepository;

class UseCampaignCharacterItemService
{
    public function execute(int $campaignCharacterId, int $itemId): void
    {
        $repo = new CampaignCharacterItemRepository();

        $row = $repo->findByCharacterAndItem(
            $campaignCharacterId,
            $itemId
        );

        if (!$row) {
            throw new ValidationException(
                'Item não encontrado.',
                ['item' => ['Este item não pertence ao personagem.']]
            );
        }

        if ((int) $row['quantity'] <= 0) {
            throw new ValidationException(
                'Item sem quantidade.',
                ['quantity' => ['Este item não possui mais usos.']]
            );
        }

        $newQuantity = (int) $row['quantity'] - 1;

        if ($newQuantity === 0) {
            $repo->updateQuantityByCharacterAndItem(
                $campaignCharacterId,
                $itemId,
                0
            );

            $repo->deactivateByCharacterAndItem(
                $campaignCharacterId,
                $itemId
            );

            return;
        }

        $repo->updateQuantityByCharacterAndItem(
            $campaignCharacterId,
            $itemId,
            $newQuantity
        );
    }
}
