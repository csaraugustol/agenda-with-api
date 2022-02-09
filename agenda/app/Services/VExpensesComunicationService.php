<?php

namespace App\Services;

use Throwable;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\ExternalTokenServiceInterface;
use App\Services\Params\Vexpenses\AccessTokenServiceParams;
use App\Services\Contracts\VExpensesComunicationServiceInterface;

class VExpensesComunicationService extends BaseService implements VExpensesComunicationServiceInterface
{
    /**
     * Retorna o token de acesso ao VExpenses
     *
     * @param AccessTokenServiceParams $accessTokenServiceParams
     *
     * @return ServiceResponse
     */
    public function tokenToAccessVexpenses(AccessTokenServiceParams $accessTokenServiceParams): ServiceResponse
    {
        try {
            $findUserResponse = app(UserServiceInterface::class)->find(
                $accessTokenServiceParams->user_id
            );
            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            $externalTokenResponse = app(ExternalTokenServiceInterface::class)
                ->storeToken(
                    $accessTokenServiceParams->token,
                    $accessTokenServiceParams->user_id,
                    $accessTokenServiceParams->system,
                    $accessTokenServiceParams->expires_at,
                    $accessTokenServiceParams->clear_rectroativics_tokens
                );

            if (!$externalTokenResponse->success) {
                return $externalTokenResponse;
            }

            $token = $externalTokenResponse->data;
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('accessTokenServiceParams'));
        }

        return new ServiceResponse(
            true,
            'Token gerado com sucesso.',
            $token
        );
    }
}
