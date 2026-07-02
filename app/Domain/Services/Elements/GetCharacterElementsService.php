<?php

namespace App\Domain\Services\Elements;

use App\Infrastructure\Repositories\CampaignCharacterArmorRepository;
use App\Infrastructure\Repositories\ArmorElementTypeRepository;
use App\Infrastructure\Repositories\ElementTypeRepository;

class GetCharacterElementsService
{
    public function execute(int $campaignCharacterId): array
    {
        $armorRepo = new CampaignCharacterArmorRepository();
        $armorElementRepo = new ArmorElementTypeRepository();
        $elementTypeRepo = new ElementTypeRepository();

        $elementIds = [];

        foreach ($armorRepo->findActiveByCampaignCharacter($campaignCharacterId) as $armorRow) {
            $isEquipped = (int) ($armorRow['is_equipped'] ?? 0) === 1;

            if (!$isEquipped) {
                continue;
            }

            foreach ($armorElementRepo->getByArmorId((int) $armorRow['armor_id']) as $elementId) {
                $elementIds[(int) $elementId] = true;
            }
        }

        if (empty($elementIds)) {
            $elementIds[1] = true;
        }

        $elements = [];

        foreach (array_keys($elementIds) as $elementId) {
            $element = $elementTypeRepo->findById((int) $elementId);

            if (!$element) {
                continue;
            }

            $elements[] = [
                'id' => $element->id,
                'name' => $element->name,
            ];
        }

        return $elements;
    }
}
