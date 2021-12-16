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

    /**
     * Retorna um contato do usuário pelo id
     * para mostrar seus detalhes
     *
     * @param string $userId
     * @param string $contactId
     *
     * @return ServiceResponse
     */
    public function findByUserContact(string $userId, string $contactId): ServiceResponse
    {
        try {
            $findContactResponse = $this->find($contactId);

            if (!$findContactResponse->success || is_null($findContactResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findContactResponse->message,
                    null,
                    $findContactResponse->internalErrors
                );
            }

            $contact = $findContactResponse->data;
            if ($contact->user_id !== $userId) {
                return new ServiceResponse(
                    false,
                    "Contato não foi localizado!",
                    null
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId', 'contactId'));
        }

        return new ServiceResponse(
            true,
            "Busca realizada com sucesso!",
            $contact
        );
    }

    /**
     * Retorna um contato pelo id
     *
     * @param string $contactId
     *
     * @return ServiceResponse
     */
    public function find(string $contactId): ServiceResponse
    {
        try {
            $contact = $this->contactRepository->findOrNull($contactId);

            if (is_null($contact)) {
                return new ServiceResponse(
                    true,
                    'O contato não foi localizado.',
                    null,
                    [
                        new InternalError(
                            'O contato não foi localizado.',
                            14
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('contactId'));
        }

        return new ServiceResponse(
            true,
            "Contato encontrado com sucesso!",
            $contact
        );
    }
}
