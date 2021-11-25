<?php

namespace App\Services;

use Throwable;
use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\UserRepository;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Params\User\RegisterUserServiceParams;

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
            //Realiza a criptografia da senha
            $passwordEncrypted =  bcrypt($params->password);

            $user = $this->userRepository->create([
                'name'     => $params->name,
                'email'    => $params->email,
                'password' => $passwordEncrypted
            ]);
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
