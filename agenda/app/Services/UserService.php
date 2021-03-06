<?php

namespace App\Services;

use Throwable;
use Illuminate\Support\Arr;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\UserRepository;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Params\User\RegisterUserServiceParams;
use App\Services\Contracts\ChangePasswordServiceInterface;
use App\Services\Contracts\AuthenticateTokenServiceInterface;

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
     * Realiza a validação do usuário para efetuar login
     *
     * @param string $email
     * @param string $password
     *
     * @return ServiceResponse
     */
    public function login(string $email, string $password): ServiceResponse
    {
        try {
            $findUserResponse = $this->findByEmail($email);

            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
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

            $user = $findUserResponse->data;
            if (!password_verify($password, $user->password)) {
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

            $authenticateTokenResponse = app(AuthenticateTokenServiceInterface::class)
                ->storeToken($user->id);

            if (!$authenticateTokenResponse->success || is_null($authenticateTokenResponse->data)) {
                return new ServiceResponse(
                    false,
                    'Ocorreu um erro ao criar o token para o acesso.',
                    null,
                    [
                        new InternalError(
                            'Ocorreu um erro ao criar o token para o acesso.',
                            2
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('email', 'password'));
        }

        return new ServiceResponse(
            true,
            'Login realizado com sucesso.',
            $authenticateTokenResponse->data
        );
    }

    /**
     * Realiza o logout do usuário no sistema
     * limpando o token
     *
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function logout(string $userId): ServiceResponse
    {
        try {
            $findUserResponse = $this->find($userId);
            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            $clearTokenResponse = app(AuthenticateTokenServiceInterface::class)
                ->clearToken($userId);

            if (!$clearTokenResponse->success) {
                return $clearTokenResponse;
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId'));
        }

        return new ServiceResponse(
            true,
            'Logout realizado com sucesso.',
            null
        );
    }

    /**
     * Busca usuário pelo email
     *
     * @param string $email
     *
     * @return ServiceResponse
     */
    public function findByEmail(string $email): ServiceResponse
    {
        try {
            $user = $this->userRepository->findUserByEmail($email);

            if (is_null($user)) {
                return new ServiceResponse(
                    true,
                    'Usuário não encontrado.',
                    null,
                    [
                        new InternalError(
                            'Usuário não encontrado.',
                            3
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('email'));
        }

        return new ServiceResponse(
            true,
            'O usuário foi encontrado com sucesso.',
            $user
        );
    }

    /**
     * Realiza alteração de dados do usuário
     *
     * @param array $params
     * @param string $id
     *
     * @return ServiceResponse
     */
    public function update(array $params, string $userId): ServiceResponse
    {
        try {
            $findUserResponse = $this->find($userId);
            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            //Retira do $params, a senha caso exista
            $data = Arr::except(
                $params,
                ['password']
            );

            $userUpdate = $this->userRepository->update(
                $data,
                $userId
            );
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('params', 'userId'));
        }

        return new ServiceResponse(
            true,
            'O usuário foi atualizado com sucesso.',
            $userUpdate
        );
    }

    /**
     * Busca o usuário pelo id
     *
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function find(string $userId): ServiceResponse
    {
        try {
            $user = $this->userRepository->findOrNull($userId);

            if (is_null($user)) {
                return new ServiceResponse(
                    true,
                    'Usuário não encontrado.',
                    null,
                    [
                        new InternalError(
                            'Usuário não encontrado.',
                            3
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId'));
        }

        return new ServiceResponse(
            true,
            'O usuário foi encontrado com sucesso.',
            $user
        );
    }

    /**
     * Retorna o token para alteração da senha
     *
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function tokenToChangePassword(string $userId): ServiceResponse
    {
        try {
            $newTokenResponse = app(ChangePasswordServiceInterface::class)
                ->newToken($userId);

            if (!$newTokenResponse->success) {
                return $newTokenResponse;
            }

            $token = $newTokenResponse->data;
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId'));
        }

        return new ServiceResponse(
            true,
            'Solicitação de alterar senha concluída com sucesso.',
            $token
        );
    }

    /**
     * Atualiza a senha do usuário
     *
     * @param string $userId
     * @param string $currentPassword
     * @param string $newPassword
     *
     * @return ServiceResponse
     */
    public function changePassword(
        string $userId,
        string $currentPassword,
        string $newPassword
    ): ServiceResponse {
        try {
            $findUserResponse = $this->find($userId);
            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            $user = $findUserResponse->data;
            if (!password_verify($currentPassword, $user->password)) {
                return new ServiceResponse(
                    false,
                    'A senha atual informada é inválida!',
                    null,
                    [
                        new InternalError(
                            'A senha atual informada é inválida!',
                            20
                        )
                    ]
                );
            }

            $userPasswordUpdate = $this->userRepository->update(
                ['password' => bcrypt($newPassword)],
                $userId
            );
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId', 'currentPassword', 'newPassword'));
        }

        return new ServiceResponse(
            true,
            'Senha atualizada com sucesso.',
            $userPasswordUpdate
        );
    }
}
