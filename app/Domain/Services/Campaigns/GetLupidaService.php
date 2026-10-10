<?php

namespace App\Domain\Services\Campaigns;

use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignCharacterArmorRepository;
use App\Infrastructure\Repositories\CampaignCharacterItemRepository;
use App\Infrastructure\Repositories\CampaignCharacterWeaponRepository;

use App\Infrastructure\Repositories\ArmorRepository;
use App\Infrastructure\Repositories\ArmorElementTypeRepository;
use App\Infrastructure\Repositories\ArmorArmorAbilityRepository;
use App\Infrastructure\Repositories\ArmorAbilityRepository;

use App\Infrastructure\Repositories\WeaponRepository;
use App\Infrastructure\Repositories\WeaponElementTypeRepository;
use App\Infrastructure\Repositories\WeaponAbilityRepository;
use App\Infrastructure\Repositories\WeaponAbilityElementTypeRepository;

use App\Infrastructure\Repositories\ItemRepository;
use App\Infrastructure\Repositories\ItemElementTypeRepository;
use App\Infrastructure\Repositories\ItemAbilityRepository;

class GetLupidaService
{
    public function execute(int $campaignId): array
    {
        $campaignRepo = new CampaignRepository();
        $campaignCharacterRepo = new CampaignCharacterRepository();

        $campaignArmorRepo = new CampaignCharacterArmorRepository();
        $campaignItemRepo = new CampaignCharacterItemRepository();
        $campaignWeaponRepo = new CampaignCharacterWeaponRepository();

        $armorRepo = new ArmorRepository();
        $armorElementRepo = new ArmorElementTypeRepository();
        $armorArmorAbilityRepo = new ArmorArmorAbilityRepository();
        $armorAbilityRepo = new ArmorAbilityRepository();

        $weaponRepo = new WeaponRepository();
        $weaponElementRepo = new WeaponElementTypeRepository();
        $weaponAbilityRepo = new WeaponAbilityRepository();
        $weaponAbilityElementRepo = new WeaponAbilityElementTypeRepository();

        $itemRepo = new ItemRepository();
        $itemElementRepo = new ItemElementTypeRepository();
        $itemAbilityRepo = new ItemAbilityRepository();

        $campaign = $campaignRepo->findById($campaignId);

        if (!$campaign) {
            throw new ValidationException(
                'Campanha inválida.',
                ['campaign_id' => ['Campanha não encontrada.']]
            );
        }

        $characters = $campaignCharacterRepo->findByCampaignId($campaignId);

        $ownedArmorIds = [];
        $ownedWeaponIds = [];
        $ownedItemIds = [];

        foreach ($characters as $character) {
            $cid = (int) $character['id'];

            foreach ($campaignArmorRepo->findActiveByCampaignCharacter($cid) as $row) {
                $ownedArmorIds[(int) $row['armor_id']] = true;
            }

            foreach ($campaignWeaponRepo->findActiveByCampaignCharacter($cid) as $row) {
                $ownedWeaponIds[(int) $row['weapon_id']] = true;
            }

            foreach ($campaignItemRepo->findActiveByCampaignCharacter($cid) as $row) {
                $ownedItemIds[(int) $row['item_id']] = true;
            }
        }

        $armors = array_values(array_filter(
            $armorRepo->findAllWithItemAndSlot(),
            fn($armor) => !isset($ownedArmorIds[(int) $armor['armor_id']])
        ));

        $weapons = array_values(array_filter(
            $weaponRepo->findAllWithItemAndDamageType(),
            fn($weapon) => !isset($ownedWeaponIds[(int) $weapon['id']])
        ));

        // Busca todos os itens não equipáveis.
        $availableItems = array_values(array_filter(
            $itemRepo->findAllNonEquipable(),
            fn($item) => !isset($ownedItemIds[(int) $item['id']])
        ));

        // Separa os itens comuns dos itens especiais de Dorag.
        $commonItems = array_values(array_filter(
            $availableItems,
            fn($item) => (int) ($item['dorag'] ?? 0) !== 1
        ));

        $doragItems = array_values(array_filter(
            $availableItems,
            fn($item) => (int) ($item['dorag'] ?? 0) === 1
        ));

        // Sorteio padrão da loja.
        shuffle($armors);
        shuffle($commonItems);
        shuffle($doragItems);

        $armors = array_slice($armors, 0, 5);
        $items = array_slice($commonItems, 0, 5);

        // IDs dos itens já selecionados.
        $listedItemIds = array_map(
            fn($item) => (int) $item['id'],
            $items
        );

        // Adiciona uma munição, mantendo a regra existente.
        $ammoItem = $weaponRepo->findRandomAmmoItemExcludingIds(
            $listedItemIds
        );

        if ($ammoItem) {
            $items[] = $ammoItem;
            $listedItemIds[] = (int) $ammoItem['id'];
        }

        // Adiciona um item Dorag, se existir algum disponível.
        // Não ocupa nenhuma das cinco vagas dos itens comuns.
        $doragItem = null;

        foreach ($doragItems as $candidate) {
            $candidateId = (int) $candidate['id'];

            if (in_array($candidateId, $listedItemIds, true)) {
                continue;
            }

            $doragItem = $candidate;
            break;
        }

        if ($doragItem !== null) {
            $items[] = $doragItem;
        }

        foreach ($armors as &$armor) {
            $armorId = (int) $armor['armor_id'];
            $itemId = (int) $armor['item_id'];

            $armor['value'] = $itemRepo->getValueById($itemId);
            $armor['elements'] = $armorElementRepo->getByArmorId($armorId);

            if ((int) $armor['armor_slot_id'] === 2) {
                $armor['weak_damage_type_id'] = $armor['weak_damage_type_id']
                    ? (int) $armor['weak_damage_type_id']
                    : null;
            }

            $abilityIds = $armorArmorAbilityRepo->getByArmorId($armorId);

            $armor['abilities'] = array_map(
                fn($id) => $armorAbilityRepo->findById($id)->toArray(),
                $abilityIds
            );
        }

        unset($armor);

        foreach ($weapons as &$weapon) {
            $weaponId = (int) $weapon['id'];
            $itemId = (int) $weapon['item_id'];

            $weapon['value'] = $itemRepo->getValueById($itemId);
            $weapon['elements'] = $weaponElementRepo->getByWeaponId($weaponId);

            $abilities = $weaponAbilityRepo->findByWeaponId($weaponId);

            foreach ($abilities as &$ability) {
                $ability->element_types =
                    $weaponAbilityElementRepo->getByWeaponAbilityId($ability->id);

                $ability = $ability->toArray();
            }

            unset($ability);

            $weapon['abilities'] = $abilities;
        }

        unset($weapon);

        $doragItemId = $doragItem !== null
            ? (int) $doragItem['id']
            : null;

        $items = array_map(function ($item) use (
            $itemRepo,
            $itemElementRepo,
            $itemAbilityRepo,
            $weaponRepo,
            $doragItemId
        ) {
            $itemId = (int) $item['id'];

            $isAmmo = $weaponRepo->existsByAmmoItemId($itemId);
            $isDorag = $doragItemId !== null
                && $itemId === $doragItemId;

            $quantity = $isDorag
                ? random_int(1, 5)
                : ($isAmmo ? 30 : random_int(1, 10));

            return [
                'item_id' => $itemId,
                'item_name' => $item['name'],
                'item_description' => $item['description'],
                'quantity' => $quantity,
                'value' => $itemRepo->getValueById($itemId),
                'elements' => $itemElementRepo->getByItemId($itemId),
                'abilities' => $itemAbilityRepo->getByItemId($itemId),
            ];
        }, $items);

        return [
            'campaign_id' => $campaignId,
            'armors' => $armors,
            'weapons' => $weapons,
            'items' => $items,
        ];
    }
}
