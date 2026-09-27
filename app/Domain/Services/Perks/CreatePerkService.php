<?php

namespace App\Domain\Services\Perks;

use App\Application\DTOs\Perks\CreatePerkDTO;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\Perk;
use App\Infrastructure\Repositories\PerkRepository;
use App\Infrastructure\Repositories\RacePerkRepository;
use App\Infrastructure\Repositories\OrderPerkRepository;
use App\Infrastructure\Repositories\PerkAttributeRepository;
use App\Infrastructure\Repositories\PerkFlagRepository;
use App\Infrastructure\Repositories\PerkElementTypeRepository;
use App\Infrastructure\Repositories\PerkAbilityRepository;

class CreatePerkService
{
    private PerkRepository $perks;
    private RacePerkRepository $racePerks;
    private OrderPerkRepository $orderPerks;
    private PerkAttributeRepository $attributes;
    private PerkFlagRepository $flags;
    private PerkElementTypeRepository $elements;
    private PerkAbilityRepository $abilities;

    public function __construct()
    {
        $this->perks =
            new PerkRepository();

        $this->racePerks =
            new RacePerkRepository();

        $this->orderPerks =
            new OrderPerkRepository();

        $this->attributes =
            new PerkAttributeRepository();

        $this->flags =
            new PerkFlagRepository();

        $this->elements =
            new PerkElementTypeRepository();

        $this->abilities =
            new PerkAbilityRepository();
    }

    public function execute(
        CreatePerkDTO $dto
    ): Perk {
        if (
            $this->perks->existsByName(
                $dto->name
            )
        ) {
            throw new ConflictException(
                'Já existe um perk com esse nome.'
            );
        }

        $this->validateRequirements($dto);

        $this->validateSheetRelations($dto);

        $perkId = $this->perks->create([
            'name' =>
                $dto->name,

            'description' =>
                $dto->description,

            'type' =>
                $dto->type,

            'mana_cost' =>
                $dto->mana_cost,
        ]);

        if (!$perkId) {
            throw new ValidationException(
                'Falha ao criar perk.'
            );
        }

        $this->attachRaceOrOrder(
            $dto,
            $perkId
        );

        $this->attachAttributes(
            $dto,
            $perkId
        );

        $this->attachFlags(
            $dto,
            $perkId
        );

        $this->attachElements(
            $dto,
            $perkId
        );

        $this->createAbility(
            $dto,
            $perkId
        );

        $this->attachRequirements(
            $dto,
            $perkId
        );

        $this->createSheet(
            $dto,
            $perkId
        );

        $this->attachToSheets(
            $dto,
            $perkId
        );

        $perk = $this->perks->findById(
            $perkId
        );

        if (!$perk) {
            throw new ValidationException(
                'Erro ao carregar perk criado.'
            );
        }

        $savedAbilities =
            $this->abilities->findByPerk(
                $perkId
            );

        $perk->race_id =
            $dto->race_id;

        $perk->order_id =
            $dto->order_id;

        $perk->required_level =
            $dto->required_level;

        $perk->attributes =
            $dto->attributes;

        $perk->flags =
            $dto->flags;

        $perk->element_types =
            $dto->element_types;

        $perk->ability =
            $savedAbilities[0] ?? null;

        return $perk;
    }

    private function validateRequirements(
        CreatePerkDTO $dto
    ): void {
        foreach (
            $dto->required_perk_ids
            as $requiredPerkId
        ) {
            if (
                !$this->perks->existsById(
                    $requiredPerkId
                )
            ) {
                throw new ValidationException(
                    'Um dos perks obrigatórios não existe.',
                    [
                        'required_perk_ids' => [
                            "Perk {$requiredPerkId} não encontrado.",
                        ],
                    ]
                );
            }
        }
    }

    private function validateSheetRelations(
        CreatePerkDTO $dto
    ): void {
        foreach (
            $dto->sheet_perk_ids
            as $sheetPerkId
        ) {
            if (
                !$this->perks->existsById(
                    $sheetPerkId
                )
            ) {
                throw new ValidationException(
                    'Um dos perks de ficha não existe.',
                    [
                        'sheet_perk_ids' => [
                            "Perk {$sheetPerkId} não encontrado.",
                        ],
                    ]
                );
            }

            if (
                !$this->perks
                    ->sheetExistsByPerkId(
                        $sheetPerkId
                    )
            ) {
                throw new ValidationException(
                    'O perk informado não possui ficha.',
                    [
                        'sheet_perk_ids' => [
                            "O perk {$sheetPerkId} não possui registro em perk_sheets.",
                        ],
                    ]
                );
            }
        }
    }

    private function attachRaceOrOrder(
        CreatePerkDTO $dto,
        int $perkId
    ): void {
        if ($dto->race_id !== null) {
            $attached =
                $this->racePerks->attachPerk(
                    $dto->race_id,
                    $perkId,
                    $dto->required_level
                );

            if (!$attached) {
                throw new ValidationException(
                    'Falha ao vincular o perk à raça.'
                );
            }
        }

        if ($dto->order_id !== null) {
            $attached =
                $this->orderPerks->attachPerk(
                    $dto->order_id,
                    $perkId,
                    $dto->required_level
                );

            if (!$attached) {
                throw new ValidationException(
                    'Falha ao vincular o perk à ordem.'
                );
            }
        }
    }

    private function attachAttributes(
        CreatePerkDTO $dto,
        int $perkId
    ): void {
        foreach (
            $dto->attributes
            as $attribute
        ) {
            $attached =
                $this->attributes->attach(
                    $perkId,
                    $attribute['name'],
                    $attribute['value']
                );

            if (!$attached) {
                throw new ValidationException(
                    'Falha ao vincular um atributo ao perk.'
                );
            }
        }
    }

    private function attachFlags(
        CreatePerkDTO $dto,
        int $perkId
    ): void {
        foreach (
            $dto->flags
            as $flag
        ) {
            $attached =
                $this->flags->attach(
                    $perkId,
                    $flag
                );

            if (!$attached) {
                throw new ValidationException(
                    'Falha ao vincular uma flag ao perk.'
                );
            }
        }
    }

    private function attachElements(
        CreatePerkDTO $dto,
        int $perkId
    ): void {
        foreach (
            $dto->element_types
            as $elementId
        ) {
            $attached =
                $this->elements->attach(
                    $perkId,
                    $elementId
                );

            if (!$attached) {
                throw new ValidationException(
                    'Falha ao vincular um tipo elemental ao perk.'
                );
            }
        }
    }

    private function createAbility(
        CreatePerkDTO $dto,
        int $perkId
    ): void {
        if (!$dto->hasAbility()) {
            return;
        }

        $abilityId =
            $this->abilities->create(
                $perkId,
                $dto->ability
            );

        if (!$abilityId) {
            throw new ValidationException(
                'Falha ao criar a habilidade do perk.'
            );
        }
    }

    private function attachRequirements(
        CreatePerkDTO $dto,
        int $perkId
    ): void {
        foreach (
            $dto->required_perk_ids
            as $requiredPerkId
        ) {
            $attached =
                $this->perks->attachRequirement(
                    $perkId,
                    $requiredPerkId
                );

            if (!$attached) {
                throw new ValidationException(
                    'Falha ao vincular requisito ao perk.'
                );
            }
        }
    }

    private function createSheet(
        CreatePerkDTO $dto,
        int $perkId
    ): void {
        if (!$dto->hasSheet()) {
            return;
        }

        $sheetId =
            $this->perks->createSheet(
                $perkId,
                $dto->sheet
            );

        if (!$sheetId) {
            throw new ValidationException(
                'Falha ao criar ficha do perk.'
            );
        }
    }

    private function attachToSheets(
        CreatePerkDTO $dto,
        int $perkId
    ): void {
        foreach (
            $dto->sheet_perk_ids
            as $sheetPerkId
        ) {
            $attached =
                $this->perks->attachToSheet(
                    $sheetPerkId,
                    $perkId
                );

            if (!$attached) {
                throw new ValidationException(
                    'Falha ao vincular o perk à ficha.'
                );
            }
        }
    }
}
