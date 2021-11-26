<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Params\User\RegisterUserServiceParams;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;

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
     *
     * POST /login
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $findUserByEmailResponse = $this->userService->findUserByEmail($request->email);

        if (!$findUserByEmailResponse->success || is_null($findUserByEmailResponse->data)) {
            return $this->errorResponseFromService($findUserByEmailResponse);
        }

        return $this->response(new DefaultResponse($findUserByEmailResponse));
    }
}
