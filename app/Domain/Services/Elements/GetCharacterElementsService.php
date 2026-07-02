<?php

namespace App\Domain\Services\Elements;

use App\Infrastructure\Repositories\CampaignCharacterArmorRepository;
use App\Infrastructure\Repositories\ArmorElementTypeRepository;

class GetCharacterElementsService
{
    public function execute(int $campaignCharacterId): array
    {
        $armorRepo = new CampaignCharacterArmorRepository();
        $armorElementRepo = new ArmorElementTypeRepository();

        $elements = [];

        foreach ($armorRepo->findActiveByCampaignCharacter($campaignCharacterId) as $armorRow) {
            $isEquipped = (int) ($armorRow['is_equipped'] ?? 0) === 1;

            if (!$isEquipped) {
                continue;
            }

            foreach ($armorElementRepo->getByArmorId((int) $armorRow['armor_id']) as $element) {
                $elements[(int) $element['id']] = $element;
            }
        }

        if (empty($elements)) {
            return [
                [
                    'id' => 1,
                    'name' => 'Normal',
                ],
            ];
        }

        return array_values($elements);
    }
}
