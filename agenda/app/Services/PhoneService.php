<?php

namespace App\Services;

use Throwable;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\PhoneRepository;
use App\Services\Contracts\PhoneServiceInterface;
use App\Services\Params\Phone\CreatePhoneServiceParams;

class PhoneService extends BaseService implements PhoneServiceInterface
{
    /**
     * @var PhoneRepository
     */
    private $phoneRepository;

    /**
     * @param PhoneRepository $phoneRepository
     */
    public function __construct(PhoneRepository $phoneRepository)
    {
        $this->phoneRepository = $phoneRepository;
    }

    /**
     * Busca um telefone pelo id
     *
     * @param string $phoneId
     *
     * @return ServiceResponse
     */
    public function find(string $phoneId): ServiceResponse
    {
        try {
            $phone = $this->phoneRepository->findOrNull($phoneId);
            if (is_nulL($phone)) {
                return new ServiceResponse(
                    true,
                    'Telefone não encontrado.',
                    null,
                    [
                        new InternalError(
                            'Telefone não encontrado.',
                            10
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('phoneId'));
        }

        return new ServiceResponse(
            true,
            'Telefone encontrado com sucesso.',
            $phone
        );
    }

    /**
     * Criação de um novo telefone
     *
     * @param CreatePhoneServiceParams $params
     *
     * @return ServiceResponse
     */
    public function store(CreatePhoneServiceParams $params): ServiceResponse
    {
        try {
            $phone = $this->phoneRepository->create($params->toArray());
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('params'));
        }

        return new ServiceResponse(
            true,
            'Telefone cadastrado com sucesso.',
            $phone
        );
    }

    /**
     * Realiza atualização do telefone
     *
     * @param array $params
     * @param string $phoneId
     *
     * @return ServiceResponse
     */
    public function update(array $params, string $phoneId): ServiceResponse
    {
        try {
            $findPhoneResponse = $this->find($phoneId);
            if (!$findPhoneResponse->success || is_null($findPhoneResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findPhoneResponse->message,
                    null,
                    $findPhoneResponse->internalErrors
                );
            }

            $phoneUpdate = $this->phoneRepository->update(
                $params,
                $phoneId
            );
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('params', 'phoneId'));
        }

        return new ServiceResponse(
            true,
            'Telefone atualizado com sucesso.',
            $phoneUpdate
        );
    }

    /**
     * Deleta um telefone pelo id
     *
     * @param string $phoneId
     *
     * @return ServiceResponse
     */
    public function delete(string $phoneId): ServiceResponse
    {
        try {
            $findPhoneResponse = $this->find($phoneId);
            if (!$findPhoneResponse->success || is_null($findPhoneResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findPhoneResponse->message,
                    null,
                    $findPhoneResponse->internalErrors
                );
            }

            $this->phoneRepository->delete($phoneId);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('phoneId'));
        }

        return new ServiceResponse(
            true,
            'Telefone removido com sucesso.',
            null
        );
    }
}
