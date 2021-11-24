<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\User\RegisterRequest;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Params\User\RegisterUserServiceParams;
use Illuminate\Support\Facades\Hash;

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
        //Faz Criptografia da senha
        $password = Hash::make($request->password);

        $params = new RegisterUserServiceParams(
            $request->name,
            $request->email,
            $password
        );

        $registerUserResponse = $this->userService->register($params);

        if (!$registerUserResponse->success) {
            return $this->errorResponseFromService($registerUserResponse);
        }

        return $this->response(new DefaultResponse());
    }
}
