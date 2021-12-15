<?php

namespace App\Services;

use Throwable;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\ContactRepository;
use App\Services\Contracts\ContactServiceInterface;

class ContactService extends BaseService implements ContactServiceInterface
{
    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * @param ContactRepository $contactRepository
     */
    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    /**
     * Busca todos os contatos do usuário podendo ser
     * usada a filtragem
     *
     * @param string $userId
     * @param array $filters
     *
     * @return ServiceResponse
     */
    public function findAllWithFilter(string $userId, array $filters = []): ServiceResponse
    {
        try {
            $contacts = $this->contactRepository->findAllWithFilter($userId, $filters);

            if (!count($contacts)) {
                return new ServiceResponse(
                    true,
                    "Não foi encontrado nenhum contato.",
                    null
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId', 'filters'));
        }

        return new ServiceResponse(
            true,
            "Contatos encontrados com sucesso",
            $contacts
        );
    }
}
