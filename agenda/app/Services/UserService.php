<?php

namespace App\Services;

use Throwable;
use App\Services\Responses\InternalError;
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

    /**
     * Busca usuário pelo email
     *
     * @param string $email
     *
     * @return ServiceResponse
     */
    public function findUserByEmail(string $email): ServiceResponse
    {
        try {
            $user = $this->userRepository->findUserByEmail($email);

            if (!$user->success || is_null($user->data)) {
                return new ServiceResponse(
                    false,
                    'Email ou senha não corresponde. Verifique as informações.',
                    null,
                    [
                        new InternalError(
                            'Email ou senha não corresponde. Verifique as informações.',
                            1
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('user'));
        }

        return new ServiceResponse(
            true,
            'O usuário foi encontrado com sucesso.',
            $user
        );
    }
}
