<?php

use App\Core\Http\Router;
use App\Application\Controllers\Users\UserController;
use App\Application\Controllers\Races\RaceController;
use App\Application\Controllers\Perks\PerkController;
use App\Application\Controllers\Orders\OrderController;
use App\Application\Controllers\Monsters\MonsterAttackController;
use App\Application\Controllers\Monsters\MonsterController;
use App\Application\Controllers\Monsters\MonsterAbilityController;
use App\Application\Controllers\Items\ItemController;
use App\Application\Controllers\Items\ItemAbilityController;
use App\Application\Controllers\Weapons\WeaponController;
use App\Application\Controllers\Weapons\WeaponAbilityController;
use App\Application\Controllers\Armors\ArmorController;
use App\Application\Controllers\Armors\ArmorAbilityController;
use App\Application\Controllers\Characters\CharacterController;
use App\Application\Controllers\Abilities\AbilityController;
use App\Application\Controllers\Campaigns\CampaignController;
use App\Application\Controllers\Campaigns\CampaignCharacterController;
use App\Application\Controllers\Campaigns\CampaignCharacterAbilityController;
use App\Application\Controllers\Campaigns\CampaignCharacterPerkController;
use App\Application\Controllers\Campaigns\CampaignCharacterItemController;
use App\Application\Controllers\Campaigns\CampaignCharacterWeaponController;
use App\Application\Controllers\Campaigns\CampaignCharacterArmorController;
use App\Application\Controllers\Elements\ElementTypeController;
use App\Application\Controllers\Encounters\EncounterController;

$router = new Router();

$router->add('POST', '/users/register', [UserController::class, 'register']);
$router->add('POST', '/users/login', [UserController::class, 'login']);
$router->middleware('auth')->add('GET', '/users/me', [UserController::class, 'me']);
$router->middleware('auth')->add('PUT', '/users/update', [UserController::class, 'update']);
$router->middleware('auth')->add('PUT', '/users/update-password', [UserController::class, 'updatePassword']);
$router->middleware('auth')->middleware('role:admin')->add('POST', '/users/give-role', [UserController::class, 'giveRole']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/races', [RaceController::class, 'store']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('PUT', '/races/update', [RaceController::class, 'update']);
$router->middleware('auth')->add('GET', '/races', [RaceController::class, 'index']);
$router->middleware('auth')->add('GET', '/races/:id', [RaceController::class, 'show']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/orders', [OrderController::class, 'store']);
$router->middleware('auth')->add('GET', '/orders', [OrderController::class, 'index']);
$router->middleware('auth')->add('GET', '/orders/:id', [OrderController::class, 'show']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/perks', [PerkController::class, 'store']);
$router->middleware('auth')->add('GET', '/races/:id/perks', [PerkController::class, 'byRace']);
$router->middleware('auth')->add('GET', '/orders/:id/perks', [PerkController::class, 'byOrder']);
$router->middleware('auth')->add('GET', '/perks/:id', [PerkController::class, 'show']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/monsters', [MonsterController::class, 'store']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/monsters/attacks', [MonsterAttackController::class, 'store']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/monsters/ability', [MonsterAbilityController::class, 'store']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/monsters/:id/attacks', [MonsterAttackController::class, 'linkToMonster']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/monsters/:id/ability', [MonsterAbilityController::class, 'linkToMonster']);
$router->middleware('auth')->add('GET', '/monsters', [MonsterController::class, 'index']);
$router->middleware('auth')->add('GET', '/monsters/:id', [MonsterController::class, 'show']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/items', [ItemController::class, 'store']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/items/ability', [ItemAbilityController::class, 'store']);
$router->middleware('auth')->add('GET', '/items', [ItemController::class, 'index']);
$router->middleware('auth')->add('GET', '/items/:id', [ItemController::class, 'show']);
$router->middleware('auth')->add('GET', '/items/:id/abilities', [ItemAbilityController::class, 'abilities']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/weapons', [WeaponController::class, 'store']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/weapons/:id/ability', [WeaponAbilityController::class, 'store']);
$router->middleware('auth')->add('GET', '/weapons', [WeaponController::class, 'index']);
$router->middleware('auth')->add('GET', '/weapons/:id', [WeaponController::class, 'show']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/armors', [ArmorController::class, 'store']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/armors/ability', [ArmorAbilityController::class, 'store']);
$router->middleware('auth')->add('GET', '/armors/ability', [ArmorAbilityController::class, 'index']);
$router->middleware('auth')->add('GET', '/armors/ability/:id', [ArmorAbilityController::class, 'show']);
$router->middleware('auth')->add('GET', '/armors', [ArmorController::class, 'index']);
$router->middleware('auth')->add('GET', '/armors/:id', [ArmorController::class, 'show']);

$router->middleware('auth')->add('POST', '/character', [CharacterController::class, 'store']);
$router->middleware('auth')->add('POST', '/character/:id/abilities', [AbilityController::class, 'store']);
$router->middleware('auth')->add('GET', '/character/:id/abilities', [AbilityController::class, 'index']);
$router->middleware('auth')->add('GET', '/character/:character_id/abilities/:ability_id', [AbilityController::class, 'show']);
$router->middleware('auth')->add('GET', '/character', [CharacterController::class, 'index']);
$router->middleware('auth')->add('GET', '/character/me', [CharacterController::class, 'myCharacters']);
$router->middleware('auth')->add('GET', '/character/:id', [CharacterController::class, 'show']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/campaign', [CampaignController::class, 'store']);
$router->middleware('auth')->add('POST', '/campaign/:id/characters', [CampaignCharacterController::class, 'store']);
$router->middleware('auth')->add('POST', '/campaign/characters/level-up', [CampaignCharacterController::class, 'levelUp']);
$router->middleware('auth')->add('POST','/campaign/characters/confirm-level-up',[CampaignCharacterController::class, 'confirmLevelUp']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/campaign/characters/xp', [CampaignCharacterController::class,'changeXP']);
$router->middleware('auth')->add('POST', '/campaign/characters/gold', [CampaignCharacterController::class,'changeGold']);
$router->middleware('auth')->add('POST', '/campaign/:id/ability', [CampaignCharacterAbilityController::class, 'store']);
$router->middleware('auth')->add('POST', '/campaign/:id/perk', [CampaignCharacterPerkController::class, 'store']);
$router->middleware('auth')->add('POST', '/campaign/:id/item', [CampaignCharacterItemController::class, 'store']);
$router->middleware('auth')->add('PUT', '/campaign/item/use', [CampaignCharacterItemController::class, 'use']);
$router->middleware('auth')->add('POST', '/campaign/:id/weapon', [CampaignCharacterWeaponController::class, 'store']);
$router->middleware('auth')->add('POST', '/campaign/:id/armor', [CampaignCharacterArmorController::class, 'store']);
$router->middleware('auth')->add('POST', '/campaign/:id/unequip-armor', [CampaignCharacterArmorController::class, 'remove']);
$router->middleware('auth')->add('GET', '/campaign', [CampaignController::class, 'index']);
$router->middleware('auth')->add('GET', '/campaign/my', [CampaignController::class, 'myCampaigns']);
$router->middleware('auth')->add('GET', '/campaign/:id', [CampaignController::class, 'show']);
$router->middleware('auth')->add('GET', '/campaign/:id/lupida', [CampaignController::class, 'getLupida']);
$router->middleware('auth')->add('GET', '/campaign/:campaign_id/character/:character_id/sheet', [CampaignController::class, 'getCharacterSheet']);
$router->middleware('auth')->add('GET', '/campaign/:campaign_id/character/:character_id/info', [CampaignController::class, 'getCharacterInfos']);

$router->middleware('auth')->add('GET', '/elements', [ElementTypeController::class, 'index']);
$router->middleware('auth')->add('GET', '/elements/:id', [ElementTypeController::class, 'show']);
$router->middleware('auth')->add('GET', '/elements/monster/:id', [ElementTypeController::class, 'getMonsterElements']);
$router->middleware('auth')->add('GET', '/elements/character/:id', [ElementTypeController::class, 'getCharacterElements']);
$router->middleware('auth')->add('POST', '/elements/damage', [ElementTypeController::class, 'calculateDamage']);
$router->middleware('auth')->add('POST', '/elements/relations', [ElementTypeController::class, 'getRelations']);
$router->middleware('auth')->add('POST', '/elements/relations/attack', [ElementTypeController::class, 'getAttackRelations']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/encounters', [EncounterController::class, 'store']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/encounters/add-monster', [EncounterController::class, 'addMonster']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/encounters/add-player', [EncounterController::class, 'addPlayer']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/encounters/set-initiative', [EncounterController::class, 'setInitiative']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('PUT', '/encounters/update-initiative', [EncounterController::class, 'updateInitiative']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('PUT', '/encounters/update-status', [EncounterController::class, 'updateStatus']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('PUT', '/encounters/update-monster-hp', [EncounterController::class, 'updateMonsterHp']);
$router->middleware('auth')->add('GET', '/encounters', [EncounterController::class, 'index']);
$router->middleware('auth')->add('GET', '/encounters/:id', [EncounterController::class, 'show']);
$router->middleware('auth')->add('GET', '/encounters/:id/participants', [EncounterController::class, 'participants']);


return $router;
