<?php

namespace App\Services;

use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\UserServiceInterface;
use App\Repositories\Contracts\ChangePasswordRepository;
use App\Services\Contracts\ChangePasswordServiceInterface;

class ChangePasswordService extends BaseService implements ChangePasswordServiceInterface
{
    /**
     * @var ChangePasswordRepository
     */
    private $changePasswordRepository;

    /**
     * @param ChangePasswordRepository $changePasswordRepository
     */
    public function __construct(ChangePasswordRepository $changePasswordRepository)
    {
        $this->changePasswordRepository = $changePasswordRepository;
    }

    /**
     * Cria um token para permitir alterar a senha
     *
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function newToken(string $userId): ServiceResponse
    {
        try {
            $findUserResponse = app(UserServiceInterface::class)->find(
                $userId
            );
            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            //Verifica se existe tokens de alteração de senha ativos
            $clearTokenResponse = $this->clearToken($userId);
            if (!$clearTokenResponse->success) {
                return $clearTokenResponse;
            }

            //Cria novo token para o usuário trocar a senha
            $token = $this->changePasswordRepository->create([
                'user_id'    => $userId,
                'token'      => Hash::make(Carbon::now() . bin2hex(random_bytes(17))),
                'expires_at' => Carbon::now()->addMinutes(config('auth.time_to_expire_update_password')),
            ]);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId'));
        }

        return new ServiceResponse(
            true,
            'Token gerado com sucesso',
            $token
        );
    }

    /**
     * Deleta tokens caso eles existam
     *
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function clearToken(string $userId): ServiceResponse
    {
        try {
            $findUserResponse = app(UserServiceInterface::class)->find(
                $userId
            );
            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            $changePasswords = $this->changePasswordRepository
                ->returnAllTokensToChangePassword($userId);

            if (count($changePasswords)) {
                //Deleta cada token existente
                foreach ($changePasswords as $token) {
                    $token->delete();
                }
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId'));
        }

        return new ServiceResponse(
            true,
            'Os tokens foram deletados com sucesso.',
            null
        );
    }

    /**
     * Busca por um token para alterar a senha
     *
     * @param string $token
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function findByToken(string $token, string $userId): ServiceResponse
    {
        try {
            $findUserResponse = app(UserServiceInterface::class)->find(
                $userId
            );
            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            $tokenToChangePassword = $this->changePasswordRepository->findByToken(
                $token,
                $userId
            );

            if (is_null($tokenToChangePassword)) {
                return new ServiceResponse(
                    true,
                    'O token informado, não foi localizado.',
                    null,
                    [
                        new InternalError(
                            'O token informado, não foi localizado.',
                            17
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('token', 'userId'));
        }

        return new ServiceResponse(
            true,
            'Token encontrado.',
            $tokenToChangePassword
        );
    }
}
