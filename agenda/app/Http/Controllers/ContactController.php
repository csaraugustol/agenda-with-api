<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\Contact\IndexRequest;
use App\Http\Requests\Contact\StoreRequest;
use App\Http\Requests\Contact\UpdateRequest;
use App\Services\Contracts\ContactServiceInterface;
use App\Http\Resources\Contact\ContactDetailsResource;
use App\Http\Resources\Contact\ContactCollectionResource;
use App\Services\Params\Contact\CreateCompleteContactsServiceParams;

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
        $showContactResponse = $this->contactService->find(
            $contactId,
            user('id')
        );

        if (!$showContactResponse->success || is_null($showContactResponse->data)) {
            return $this->errorResponseFromService($showContactResponse);
        }

        return $this->response(new DefaultResponse(
            new ContactDetailsResource($showContactResponse->data)
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
        $completeContactParams = new CreateCompleteContactsServiceParams(
            $request->name,
            user('id'),
            $request->phones,
            $request->adresses,
            $request->tags
        );

        $createCompleteContactResponse = $this->contactService->store(
            $completeContactParams
        );

        if (!$createCompleteContactResponse->success || is_null($createCompleteContactResponse->data)) {
            return $this->errorResponseFromService($createCompleteContactResponse);
        }

        return $this->response(new DefaultResponse(
            new ContactDetailsResource($createCompleteContactResponse->data)
        ));
    }

    /**
     * Atualiza nome de um contato
     *
     * PATCH /contacts/update/{id}
     *
     * @param UpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, string $contactId): JsonResponse
    {
        $updateContactResponse = $this->contactService->update(
            $request->name,
            $contactId,
            user('id')
        );

        if (!$updateContactResponse->success || is_null($updateContactResponse->data)) {
            return $this->errorResponseFromService($updateContactResponse);
        }

        return $this->response(new DefaultResponse(
            new ContactDetailsResource($updateContactResponse->data)
        ));
    }

    /**
     * Deleta um contato pelo id
     *
     * DELETE /contacts/{id}
     *
     * @param string $contactId
     *
     * @return JsonResponse
     */
    public function delete(string $contactId): JsonResponse
    {
        $deleteContactResponse = $this->contactService->delete($contactId, user('id'));

        if (!$deleteContactResponse->success) {
            return $this->errorResponseFromService($deleteContactResponse);
        }

        return $this->response(new DefaultResponse());
    }
}
