<?php

namespace App\Application\Controllers\Users;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Users\UserRegisterDTO;
use App\Application\DTOs\Users\UserUpdatePasswordDTO;
use App\Domain\Services\Users\CreateUserService;
use App\Domain\Services\Users\UpdateUserService;
use App\Application\DTOs\Users\UserLoginDTO;
use App\Application\DTOs\Users\UserUpdateDTO;
use App\Domain\Services\Users\LoginUserService;
use App\Domain\Services\Users\UpdateUserPasswordService;
use App\Domain\Services\Users\GetAuthenticatedUserService;
use App\Domain\Services\Users\GiveUserRoleService;
use App\Core\Security\TokenService;

class UserController
{
    public function register(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            "first_name" => "string|required|min:2",
            "last_name"  => "string|required|min:2",
            "email"      => "string|required",
            "password"   => "string|required|min:6",
            "nickname"   => "string",
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
            "password" => "string|required",
        ]);

        $schema->handle($request->body());

        $dto = new UserLoginDTO($request->body());

        $service = new LoginUserService();
        $user = $service->execute($dto);

        $token = 'Bearer ' . TokenService::generate([
            "user_id" => $user->id,
            "email"   => $user->email,
        ]);


        return Response::json([
            "message" => "Login realizado com sucesso!",
            "token" => $token,
            "user" => $user->toArray(),
        ]);
    }
    public function update(Request $request)
    {
        $authUser = $request->user();

        $schema = new ValidateSchemaMiddleware([
            "first_name" => "string|required|min:2",
            "last_name"  => "string|required|min:2",
            "email"      => "string|required",
            "nickname"   => "string",
        ]);

        $schema->handle($request->body());

        $dto = new UserUpdateDTO($request->body());

        $service = new UpdateUserService();
        $user = $service->execute($authUser->id, $dto);

        return Response::json([
            "message" => "Usuário atualizado com sucesso!",
            "user" => $user->toArray(),
        ], 200);
    }

    public function updatePassword(Request $request)
    {
        $authUser = $request->user();

        $schema = new ValidateSchemaMiddleware([
            'current_password' => 'string|required|min:6',
            'new_password' => 'string|required|min:6',
        ]);

        $schema->handle($request->body());

        $dto = new UserUpdatePasswordDTO($request->body());

        $service = new UpdateUserPasswordService();
        $service->execute($authUser->id, $dto);

        return Response::json([
            'message' => 'Senha atualizada com sucesso.',
        ]);
    }

    public function me(Request $request)
    {
        $authUser = $request->user();

        $service = new GetAuthenticatedUserService();
        $data = $service->execute($authUser->id);

        return Response::json($data);
    }

    public function giveRole(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'user_id' => 'int|required',
            'role'    => 'string|required',
        ]);

        $schema->handle($request->body());

        $service = new GiveUserRoleService();
        $service->execute(
            $request->body()['user_id'],
            $request->body()['role']
        );

        return Response::json([
            'message' => 'Role atribuída com sucesso.',
        ]);
    }



}
