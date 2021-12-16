<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\Contact\IndexRequest;
use App\Services\Contracts\ContactServiceInterface;
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
}
