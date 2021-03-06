<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\Tag\IndexRequest;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\Tag\StoreRequest;
use App\Http\Resources\Tag\TagResource;
use App\Http\Requests\Tag\UpdateRequest;
use App\Services\Contracts\TagServiceInterface;
use App\Http\Requests\Tag\AttachOrDetachRequest;
use App\Http\Resources\Tag\TagCollectionResource;
use App\Services\Params\Tag\CreateTagServiceParams;
use App\Http\Resources\TagContact\TagContactIndexResource;
use App\Services\Contracts\TagContactServiceInterface;

class TagController extends ApiController
{
    /**
     * @var TagServiceInterface
     */
    protected $tagService;

    /**
     * @param TagServiceInterface $tagService
     */
    public function __construct(TagServiceInterface $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Lista todas as tags do usuário
     *
     * @param IndexRequest $request

     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $findAllTagResponse = $this->tagService->findAll(
            user('id'),
            $request->description
        );

        if (!$findAllTagResponse->success || is_null($findAllTagResponse->data)) {
            return $this->errorResponseFromService($findAllTagResponse);
        }

        return $this->response(new DefaultResponse(
            new TagCollectionResource($findAllTagResponse->data)
        ));
    }

    /**
     * Registra uma nova tag para o usuário logado
     *
     * POST /store
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params = new CreateTagServiceParams(
            $request->description,
            user('id')
        );

        $storeTagResponse = $this->tagService->store($params);

        if (!$storeTagResponse->success || is_null($storeTagResponse->data)) {
            return $this->errorResponseFromService($storeTagResponse);
        }

        return $this->response(new DefaultResponse(
            new TagResource($storeTagResponse->data)
        ));
    }

    /**
     * Edita a tag de um usuário
     *
     * PATCH /tags/{id}
     *
     * @param UpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        $updateTagResponse = $this->tagService->update(
            $request->toArray(),
            $request->id,
            user('id')
        );

        if (!$updateTagResponse->success || is_null($updateTagResponse->data)) {
            return $this->errorResponseFromService($updateTagResponse);
        }

        return $this->response(new DefaultResponse(
            new TagResource($updateTagResponse->data)
        ));
    }

    /**
     * Deleta uma tag do usuário
     *
     * DELETE /tags/{id}
     *
     * @param string $idTag
     *
     * @return JsonResponse
     */
    public function delete(string $idTag): JsonResponse
    {
        $deleteTagResponse = $this->tagService->delete($idTag, user('id'));

        if (!$deleteTagResponse->success) {
            return $this->errorResponseFromService($deleteTagResponse);
        }

        return $this->response(new DefaultResponse());
    }

    /**
     * Vincula uma tag a um contato
     *
     * POST /tags/{id}/attach
     *
     * @param string $idTag
     * @param AttachOrDetachRequest $request
     *
     * @return JsonResponse
     */
    public function attach(string $idTag, AttachOrDetachRequest $request): JsonResponse
    {
        $attachTagContactResponse = app(TagContactServiceInterface::class)->attach(
            $idTag,
            $request->contact_id,
            user('id')
        );

        if (!$attachTagContactResponse->success || is_null($attachTagContactResponse->data)) {
            return $this->errorResponseFromService($attachTagContactResponse);
        }

        return $this->response(new DefaultResponse(
            new TagContactIndexResource($attachTagContactResponse->data)
        ));
    }

    /**
     * Desvincula uma tag de um contato
     *
     * POST /tags/{id}/detach
     *
     * @param AttachOrDetachRequest $request
     *
     * @return JsonResponse
     */
    public function detach(string $tagId, AttachOrDetachRequest $request): JsonResponse
    {
        $attachTagContactResponse = app(TagContactServiceInterface::class)->detach(
            $tagId,
            $request->contact_id,
            user('id')
        );

        if (!$attachTagContactResponse->success) {
            return $this->errorResponseFromService($attachTagContactResponse);
        }

        return $this->response(new DefaultResponse());
    }
}
