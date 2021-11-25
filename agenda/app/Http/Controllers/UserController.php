<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\User\RegisterRequest;
use App\Models\User;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Params\User\RegisterUserServiceParams;

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
     * Lista todos os usuários
     *
     * GET /users
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = new User(
            ['name'    => 'Cesar',
            'email'    => 'c@c',
            'password' => '123'],
        );

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
}
