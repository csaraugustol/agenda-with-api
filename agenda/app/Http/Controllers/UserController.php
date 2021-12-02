<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Params\User\RegisterUserServiceParams;
use App\Http\Resources\AuthenticateToken\AuthenticateTokenResource;

class UserController extends ApiController
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Página index com todos os usuários
     *
     * GET /users
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = new User(['name' => 'Teste', 'email' => 'teste@teste', 'password' => '123456']);

        return $this->response(new DefaultResponse($user));
    }

    /**
     * Registro de novo usuário
     *
     * PÒST /register
     *
     * @param RegisterRequest $request
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $params = new RegisterUserServiceParams(
            $request->name,
            $request->email,
            $request->password
        );

        $registerUserResponse = $this->userService->register($params);

        if (!$registerUserResponse->success || is_null($registerUserResponse->data)) {
            return $this->errorResponseFromService($registerUserResponse);
        }

        return $this->response(new DefaultResponse($registerUserResponse->data));
    }

    /**
     * Efetua login do usuário no sistema
     * retornando o token de acesso
     *
     * POST /login
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $findTokenResponse = $this->userService->login(
            $request->email,
            $request->password
        );

        if (!$findTokenResponse->success || is_null($findTokenResponse->data)) {
            return $this->errorResponseFromService($findTokenResponse);
        }

        return $this->response(new DefaultResponse(
            new AuthenticateTokenResource($findTokenResponse->data)
        ));
    }
}
