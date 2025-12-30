<?php

namespace App\Application\Controllers\Characters;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Characters\CreateCharacterDTO;
use App\Domain\Services\Characters\CreateCharacterService;
use App\Domain\Services\Characters\GetAllCharactersService;
use App\Domain\Services\Characters\GetCharacterByIdService;
use App\Domain\Services\Characters\GetCharactersByUserService;

class CharacterController
{
public function store(Request $request)
{
    $authUser = $request->user();

    $schema = new ValidateSchemaMiddleware([
        'name' => 'string|required|min:2',
        'description' => 'string',
        'race_id' => 'int',
        'order_id' => 'int',
        'mana_modifier' => 'string|required',
    ]);

    $schema->handle($request->body());

    $dto = new CreateCharacterDTO($request->body());

    $service = new CreateCharacterService();
    $character = $service->execute($dto, $authUser->id);

    return Response::json([
        'message' => 'Personagem criado com sucesso.',
        'character' => $character,
    ], 201);
}


    public function index()
    {
        $service = new GetAllCharactersService();

        return Response::json([
            'characters' => $service->execute()
        ]);
    }

    public function show(Request $request)
    {
        $characterId = (int) ($request->params()['id'] ?? 0);

        $service = new GetCharacterByIdService();
        $character = $service->execute($characterId);

        return Response::json([
            'character' => $character
        ]);
    }

    public function myCharacters(Request $request)
{
    $authUser = $request->user();

    $service = new GetCharactersByUserService();
    $characters = $service->execute($authUser->id);

    return Response::json([
        'characters' => $characters
    ]);
}

}
