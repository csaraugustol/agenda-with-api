<?php

namespace App\Services;

use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\UserServiceInterface;
use App\Repositories\Contracts\ExternalTokenRepository;
use App\Services\Contracts\ExternalTokenServiceInterface;

class ExternalTokenService extends BaseService implements ExternalTokenServiceInterface
{
    /**
     * @var ExternalTokenRepository
     */
    private $externalTokenRepository;

    /**
     * @param ExternalTokenRepository $externalTokenRepository
     */
    public function __construct(ExternalTokenRepository $externalTokenRepository)
    {
        $this->externalTokenRepository = $externalTokenRepository;
    }

    /**
     * Cria um token para acessar a integração com o VExpenses
     *
     * @param string $userId
     * @param string $system
     *
     * @return ServiceResponse
     */
    public function storeToken(string $userId, string $system): ServiceResponse
    {
        try {
            $findUserResponse = app(UserServiceInterface::class)->find($userId);
            if (!$findUserResponse->success && is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            $clearTokenResponse = $this->clearToken($userId);
            if (!$clearTokenResponse->success) {
                return $clearTokenResponse;
            }

            $token = $this->externalTokenRepository->create([
                'token'      => Hash::make(Carbon::now() . bin2hex(random_bytes(17))),
                'expires_at' => Carbon::now()->addMinutes(config('auth.time_to_expire_access_vexpenses')),
                'system'     => $system,
                'user_id'    => $userId
            ]);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId'));
        }

        return new ServiceResponse(
            true,
            'Token criado com sucesso.',
            $token
        );
    }

    /**
     * Limpa todos os tokens referentes a External Token, vinculados ao usuário
     *
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function clearToken(string $userId): ServiceResponse
    {
        try {
            $findUserResponse = app(UserServiceInterface::class)->find($userId);
            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            $externalTokens = $this->externalTokenRepository
                ->returnAllExternalTokensOfUser($userId);

            if (count($externalTokens)) {
                //Deleta cada token existente
                foreach ($externalTokens as $token) {
                    $token->delete();
                }
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId'));
        }

        return new ServiceResponse(
            true,
            'Tokens deletados com sucesso!',
            null
        );
    }
}
