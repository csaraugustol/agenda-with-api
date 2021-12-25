<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\User\RegisterRequest;
use App\Services\Contracts\UserServiceInterface;
use App\Http\Requests\User\UpdatePasswordRequest;
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

    /**
     * Efetua logout do sistema
     *
     * GET /logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $logoutUserResponse = $this->userService->logout(user('id'));

        if (!$logoutUserResponse->success) {
            return $this->errorResponseFromService($logoutUserResponse);
        }

        return $this->response(new DefaultResponse());
    }

    /**
     * Mostra detalhes do usuário
     *
     * GET /users
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $showUserResponse = $this->userService->find(user('id'));
        if (!$showUserResponse->success || is_null($showUserResponse->data)) {
            return $this->errorResponseFromService($showUserResponse);
        }

        return $this->response(new DefaultResponse(
            new UserResource($showUserResponse->data)
        ));
    }

    /**
     * Realiza a atualização de dados do usuário
     *
     * PATCH /users
     *
     * @return JsonResponse
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        $updateUserResponse = $this->userService->update(
            $request->toArray(),
            user('id')
        );

        if (!$updateUserResponse->success || is_null($updateUserResponse->data)) {
            return $this->errorResponseFromService($updateUserResponse);
        }

        return $this->response(new DefaultResponse(
            new UserResource($updateUserResponse->data)
        ));
    }

    /**
     * Atualiza senha do usuário
     *
     * POST /users/change-password
     *
     * @param UpdatePasswordRequest $request
     *
     * @return JsonResponse
     */
    public function changePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $changePasswordResponse = $this->userService->changePassword(
            $request->password,
            user('id')
        );

        if (!$changePasswordResponse->success || is_null($changePasswordResponse->data)) {
            return $this->errorResponseFromService($changePasswordResponse);
        }

        return $this->response(new DefaultResponse(
            new UserResource($changePasswordResponse->data)
        ));
    }
}
