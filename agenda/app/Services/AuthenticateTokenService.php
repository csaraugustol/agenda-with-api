<?php

namespace App\Services;

use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\AuthenticateTokenRepository;
use App\Services\Contracts\AuthenticateTokenServiceInterface;

class AuthenticateTokenService extends BaseService implements AuthenticateTokenServiceInterface
{
    /**
     * @var AuthenticateTokenRepository
     */
    private $authenticateTokenRepository;

    /**
     * @param AuthenticateTokenRepository $authenticateTokenRepository
     */
    public function __construct(AuthenticateTokenRepository $authenticateTokenRepository)
    {
        $this->authenticateTokenRepository = $authenticateTokenRepository;
    }

    /**
     * Registra o token do usuário
     *
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function storeToken(string $userId): ServiceResponse
    {
        try {
            //Verifica se existe tokens do usuário ativo
            $clearTokenResponse = $this->clearToken($userId);
            if (!$clearTokenResponse->success) {
                return $clearTokenResponse;
            }

            //Cria novo token para o usuário
            $newToken = $this->authenticateTokenRepository->create([
                'token'      => Hash::make(Carbon::now() . bin2hex(random_bytes(17))),
                'expires_at' => Carbon::now()->addMinutes(config('auth.time_to_expire_login')),
                'user_id'    => $userId
            ]);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId'));
        }

        return new ServiceResponse(
            true,
            'Token criado com sucesso.',
            $newToken
        );
    }

    /**
     * Busca tokens ativos do usuário e deleta
     *
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function clearToken(string $userId): ServiceResponse
    {
        try {
            $authenticateTokens = $this->authenticateTokenRepository
                ->returnAllUserTokens($userId);

            if (count($authenticateTokens)) {
                //Para cada token existe faz a deleção
                foreach ($authenticateTokens as $token) {
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

    /**
     * Valida token do usuário
     *
     * @param string $token
     *
     * @return ServiceResponse
     */
    public function validateToken(string $token): ServiceResponse
    {

        try {
            $findTokenResponse = $this->authenticateTokenRepository->findByField('token', $token);
            //dd($findTokenResponse);
            if (!count($findTokenResponse)) {
                return new ServiceResponse(
                    false,
                    'Erro ao localizar o token.',
                    null,
                    [
                        new InternalError(
                            'Erro ao localizar o token.',
                            5
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('token'));
        }

        return new ServiceResponse(
            true,
            'Token encontrado!',
            $findTokenResponse
        );
    }
}
