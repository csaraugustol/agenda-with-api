<?php

namespace App\Services;

use Throwable;
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
     * @param string      $userId
     * @param string|null $filter
     *
     * @return ServiceResponse
     */
    public function findAllWithFilter(string $userId, string $filter = null): ServiceResponse
    {
        try {
            $contacts = $this->contactRepository->findAllWithFilter($userId, $filter);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId', 'filters'));
        }

        return new ServiceResponse(
            true,
            "Busca aos contatos realizada com sucesso.",
            $contacts
        );
    }
}
