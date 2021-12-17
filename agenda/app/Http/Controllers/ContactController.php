<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\Contact\IndexRequest;
use App\Http\Requests\Contact\StoreRequest;
use App\Services\Contracts\ContactServiceInterface;
use App\Http\Resources\Contact\ContactShowResource;
use App\Http\Resources\Contact\ContactCollectionResource;

class ContactController extends ApiController
{
    /**
     * @var ContactServiceInterface
     */
    protected $contactService;

    /**
     * @param ContactServiceInterface $contactService
     */
    public function __construct(ContactServiceInterface $contactService)
    {
        $this->contactService = $contactService;
    }

    /**
     * Retorna todos os contatos do usuário logado
     * e filtra por nome e telefone
     *
     * @param IndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $findContactsResponse = $this->contactService->findAllWithFilter(
            user('id'),
            $request->filter
        );

        if (!$findContactsResponse->success) {
            return $this->errorResponseFromService($findContactsResponse);
        }

        return $this->response(new DefaultResponse(
            new ContactCollectionResource($findContactsResponse->data)
        ));
    }

    /**
     * Mostra detalhes de um contato
     *
     * GET /contacts/{id}
     *
     * @param string $idContact
     *
     * @return JsonResponse
     */
    public function show(string $contactId): JsonResponse
    {
        $showContactResponse = $this->contactService->findByUserContact(
            user('id'),
            $contactId
        );

        if (!$showContactResponse->success || is_null($showContactResponse->data)) {
            return $this->errorResponseFromService($showContactResponse);
        }

        return $this->response(new DefaultResponse(
            new ContactShowResource($showContactResponse->data)
        ));
    }

    /**
     * Cria um contato
     *
     * POST /contacts/store
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        return $this->response(new DefaultResponse());
    }
}
