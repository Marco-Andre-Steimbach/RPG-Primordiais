<?php

namespace App\Domain\Models;

class CampaignCharacterSheet
{
    private array $baseAttributes;
    private array $raceAttributes;
    private array $orderAttributes;
    private array $perkAttributes;
    private array $finalAttributes;

    public function __construct(
        public int $campaign_character_id,
        public int $level,
        public string $mana_modifier,
        array $baseAttributes,
        array $raceAttributes = [],
        array $orderAttributes = [],
        array $perkAttributes = [],
        public int $sanity_max,
        public int $sanity_current
    ) {
        $this->baseAttributes  = $this->normalizeAttributes($baseAttributes);
        $this->raceAttributes  = $this->normalizeAttributes($raceAttributes);
        $this->orderAttributes = $this->normalizeAttributes($orderAttributes);
        $this->perkAttributes  = $this->normalizeAttributes($perkAttributes);
        $this->finalAttributes = $this->calculateFinalAttributes();
    }

    private function normalizeAttributes(array $attributes): array
    {
        return [
            'str'  => (int) ($attributes['str']  ?? 0),
            'dex'  => (int) ($attributes['dex']  ?? 0),
            'con'  => (int) ($attributes['con']  ?? 0),
            'intt' => (int) ($attributes['intt'] ?? 0),
            'wis'  => (int) ($attributes['wis']  ?? 0),
            'cha'  => (int) ($attributes['cha']  ?? 0),
        ];
    }

    private function calculateFinalAttributes(): array
    {
        $final = [];

        foreach ($this->baseAttributes as $key => $value) {
            $final[$key]
                = $value
                + $this->raceAttributes[$key]
                + $this->orderAttributes[$key]
                + $this->perkAttributes[$key];
        }

        return $final;
    }

    public function getBaseAttributes(): array
    {
        return $this->baseAttributes;
    }

    public function getRaceAttributes(): array
    {
        return $this->raceAttributes;
    }

    public function getOrderAttributes(): array
    {
        return $this->orderAttributes;
    }

    public function getPerkAttributes(): array
    {
        return $this->perkAttributes;
    }

    public function getFinalAttributes(): array
    {
        return $this->finalAttributes;
    }

    public function getModifier(int $value): int
    {
        $v = max(0, $value);

        if ($v <= 3) {
            return -7;
        }

        return intdiv($v - 4, 3) - 6;
    }

    public function getModifiers(): array
    {
        $mods = [];

        foreach ($this->finalAttributes as $key => $value) {
            $mods[$key] = $this->getModifier($value);
        }

        return $mods;
    }

    public function getManaModifierValue(): int
    {
        return match ($this->mana_modifier) {
            'str'  => $this->getModifier($this->finalAttributes['str']),
            'dex'  => $this->getModifier($this->finalAttributes['dex']),
            'con'  => $this->getModifier($this->finalAttributes['con']),
            'int'  => $this->getModifier($this->finalAttributes['intt']),
            'wis'  => $this->getModifier($this->finalAttributes['wis']),
            'cha'  => $this->getModifier($this->finalAttributes['cha']),
            default => 0,
        };
    }

    public function getMaxHp(): int
    {
        return max(
            1,
            ($this->getModifier($this->finalAttributes['con']) * $this->level) + 10
        );
    }

    public function getMaxMana(): int
    {
        $level = max(1, $this->level);

        $modifier = $this->getManaModifierValue();
        if ($modifier < 1) {
            $modifier = 1;
        }

        return (int) floor((($level / 2) * $modifier) + 10);
    }

    public function getBaseArmorClass(): int
    {
        return $this->getModifier($this->finalAttributes['dex']) + 5;
    }

    public function getSanityMax(): int
    {
        return max(15, $this->sanity_max);
    }

    public function toArray(): array
    {
        return [
            'campaign_character_id' => $this->campaign_character_id,
            'level' => $this->level,

            'attributes' => [
                'base'  => $this->getBaseAttributes(),
                'race'  => $this->getRaceAttributes(),
                'order' => $this->getOrderAttributes(),
                'perk'  => $this->getPerkAttributes(),
                'final' => $this->getFinalAttributes(),
            ],

            'modifiers' => $this->getModifiers(),

            'hp_max' => $this->getMaxHp(),
            'mana_max' => $this->getMaxMana(),
            'base_ca' => $this->getBaseArmorClass(),

            'sanity' => [
                'current' => $this->sanity_current,
                'max' => $this->getSanityMax(),
            ],
        ];
    }
}
