<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\AuthenticateToken;

trait Authentication
{
    /**
     * Cria um usuário com autenticação e cria um contato para ele
     *
     * @return object
     */
    protected function generateUserAndToken(): object
    {
        $password = $this->faker->password;
        $user = factory(User::class)->create([
            'password' => bcrypt($password)
        ]);

        $authenticateToken = factory(AuthenticateToken::class)->create([
            'user_id' => $user->id
        ]);

        return (object) [
            'user'     => $user,
            'password' => $password,
            'token'    => 'Bearer ' . $authenticateToken->token,
        ];
    }

    /**
     * Gera um token aleatório para ser utilizado como token inválido
     *
     * @return string
     */
    protected function generateUnauthorizedToken(): string
    {
        $token = $this->faker->sha1;
        return 'Bearer ' . $token;
    }
}
