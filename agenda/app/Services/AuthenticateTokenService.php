<?php

namespace App\Services;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
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
     * @param User $user
     *
     * @return ServiceResponse
     */
    public function storeToken(User $user): ServiceResponse
    {
        try {
            //Verifica se existe tokens do usuário
            $token = $this->authenticateTokenRepository->verifyExistsToken($user->id);

            if (!is_null($token)) {
                //Para cada token existe faz a deleção
                foreach ($token->data as $token) {
                    $token = $this->authenticateTokenRepository->update(
                        [
                            'deleted_at'     => Carbon::now()
                        ],
                        $user->id
                    );
                }
            }

            //Cria novo token para o usuário
            $token = $this->authenticateTokenRepository->create([
                'token'     => 'teste',
                'user_id' => $user->id
            ]);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('token'));
        }

        return new ServiceResponse(
            true,
            'Token criado com sucesso.',
            $token
        );
    }
}
