<?php

namespace App\Services;

use Throwable;
use Carbon\Carbon;
use App\Services\Responses\InternalError;
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
     * Cria um token para acessar a integração
     *
     * @param string       $token
     * @param string       $userId
     * @param string       $system
     * @param Carbon|null  $expiresAt
     * @param boolean|true $clearRectroativicsTokens
     *
     * @return ServiceResponse
     */
    public function storeToken(
        string $token,
        string $userId,
        string $system,
        Carbon $expiresAt = null,
        bool $clearRectroativicsTokens = true
    ): ServiceResponse {
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

            //Verifica se será necessário limpar os tokens retroativos
            if ($clearRectroativicsTokens) {
                $clearTokenResponse = $this->clearToken($userId, $system);
                if (!$clearTokenResponse->success) {
                    return $clearTokenResponse;
                }
            }

            $token = $this->externalTokenRepository->create([
                'token'      => $token,
                'expires_at' => $expiresAt,
                'system'     => $system,
                'user_id'    => $userId
            ]);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn(
                $throwable,
                compact('token', 'userId', 'system', 'expiresAt', 'clearRectroativicsTokens')
            );
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
     * @param string $system
     *
     * @return ServiceResponse
     */
    public function clearToken(string $userId, string $system): ServiceResponse
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
                ->returnAllExternalTokens($userId, $system);

            if (count($externalTokens)) {
                //Deleta cada token existente
                foreach ($externalTokens as $token) {
                    $token->delete();
                }
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId', 'system'));
        }

        return new ServiceResponse(
            true,
            'Tokens deletados com sucesso!',
            null
        );
    }

    /**
     * Retorna Token de validação para acesso a integração
     *
     * @param string $userId
     * @param string $system
     *
     * @return ServiceResponse
     */
    public function findByToken(string $userId, string $system): ServiceResponse
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

            $externalToken = $this->externalTokenRepository->findByToken(
                $userId,
                $system
            );

            if (is_null($externalToken)) {
                return new ServiceResponse(
                    true,
                    'O External Token não foi localizado.',
                    null,
                    [
                        new InternalError(
                            'O External Token não foi localizado.',
                            28
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId', 'system'));
        }

        return new ServiceResponse(
            true,
            'Token retornado com sucesso.',
            $externalToken
        );
    }
}
