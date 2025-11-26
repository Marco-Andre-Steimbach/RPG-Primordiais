<?php

namespace App\Application\Controllers\Users;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Users\UserRegisterDTO;
use App\Domain\Services\Users\CreateUserService;
use App\Application\DTOs\Users\UserLoginDTO;
use App\Domain\Services\Users\LoginUserService;


class UserController
{
    public function register(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            "first_name" => "string|required|min:2",
            "last_name"  => "string|required|min:2",
            "email"      => "string|required",
            "password"   => "string|required|min:6",
            "nickname"   => "string"
        ]);

        $schema->handle($request->body());

        $dto = new UserRegisterDTO($request->body());

        $service = new CreateUserService();
        $user = $service->execute($dto);

        return Response::json([
            "message" => "Usuário criado com sucesso!",
            "user" => $user->toArray(),
        ], 201);
    }
    public function login(Request $request)
{
    $schema = new ValidateSchemaMiddleware([
        "login"    => "string|required",
        "password" => "string|required"
    ]);

    $schema->handle($request->body());

    $dto = new UserLoginDTO($request->body());

    $service = new LoginUserService();
    $user = $service->execute($dto);

    return Response::json([
        "message" => "Login realizado com sucesso!",
        "user" => $user->toArray()
    ]);
}

}
