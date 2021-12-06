<?php

use App\Models\User;
use App\Services\Contracts\AuthenticateTokenServiceInterface;

if (!function_exists('user')) {
    /**
     * Retorna os dados do usuário solicitado via request
     *
     * @param string $attribute
     * @return null|User|string
     */
    function user(string $attribute = null)
    {
        if (!request()) {
            return null;
        }

        //Token do usuário logado
        $token = request()->bearerToken();

        $user = null;

        $findTokenResponse = app(AuthenticateTokenServiceInterface::class)
            ->findToken($token);

        if (!is_null($findTokenResponse->data)) {
            $user = $findTokenResponse->data->user;
        }

        if (is_null($user) || is_null($attribute)) {
            return $user;
        }

        return $user->getAttributes()[$attribute];
    }
}
