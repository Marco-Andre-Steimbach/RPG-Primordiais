<?php

namespace App\Domain\Services\Campaigns;

class GetCampaignCharacterPerkSheetsService
{
    public function execute(
        int $campaignId,
        int $characterId
    ): array {
        $sheetService =
            new GetCampaignCharacterSheetService();

        $sheet =
            $sheetService->execute(
                $campaignId,
                $characterId
            );

        return $sheet['perk_sheets']
            ?? [];
    }
}
