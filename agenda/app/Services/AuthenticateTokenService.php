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
            $findUserResponse = $this->clearToken($user->id);

            //Cria novo token para o usuário
            $newToken = $this->authenticateTokenRepository->create([
                'token'     => 'Teste',
                'expires_at' => Carbon::tomorrow('America/Sao_Paulo'),
                'user_id' => $user->id
            ]);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('newToken'));
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
     * @param string $id
     *
     * @return ServiceResponse
     */
    public function clearToken(string $id): ServiceResponse
    {
        $authenticateTokens = $this->authenticateTokenRepository->verifyExistsToken($id);

        if (count($authenticateTokens)) {
            //Para cada token existe faz a deleção
            foreach ($authenticateTokens->toArray() as $token) {
                $teste = app(AuthenticateTokenRepository::class)->update([
                    'deleted_at' => Carbon::now()
                ], $id);
                dd($teste->toArray());
            }
        }

        return new ServiceResponse(
            true,
            'Tokens deletados com sucesso!',
            $authenticateTokens
        );
    }
}
