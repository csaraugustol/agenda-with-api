<?php

namespace App\Services;

use Throwable;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\ExternalTokenServiceInterface;
use App\Services\Contracts\VExpensesComunicationServiceInterface;

class VExpensesComunicationService extends BaseService implements VExpensesComunicationServiceInterface
{
    /**
     * Retorna o token de acesso ao VExpenses
     *
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function tokenToAccessVExpenses(string $userId): ServiceResponse
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

            $externalTokenResponse = app(ExternalTokenServiceInterface::class)
            ->storeToken($userId);

            if (!$externalTokenResponse->success) {
                return $externalTokenResponse;
            }

            $token = $externalTokenResponse->data;
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId'));
        }

        return new ServiceResponse(
            true,
            'Token gerado com sucesso.',
            $token
        );
    }
}
