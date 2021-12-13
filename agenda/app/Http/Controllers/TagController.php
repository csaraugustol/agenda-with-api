<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\Tag\StoreRequest;
use App\Http\Resources\Tag\TagResource;
use App\Services\Contracts\TagServiceInterface;
use App\Services\Params\Tag\CreateTagServiceParams;

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

    /** Registra uma nova tag para o usuário logado
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
}
