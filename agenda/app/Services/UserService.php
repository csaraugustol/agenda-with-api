<?php

namespace App\Services;

use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\UserRepository;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Params\User\RegisterUserServiceParams;
use Throwable;

class UserService extends BaseService implements UserServiceInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Registra um novo usuário
     *
     * @param RegisterUserServiceParams $params
     *
     * @return ServiceResponse
     */
    public function register(RegisterUserServiceParams $params): ServiceResponse
    {
        try {
            $user = $this->userRepository->create($params->toArray());
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('params'));
        }

        return new ServiceResponse(
            true,
            'Registro realizado com sucesso.',
            $user
        );
    }
}
