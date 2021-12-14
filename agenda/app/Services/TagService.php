<?php

namespace App\Services;

use Throwable;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\TagRepository;
use App\Services\Contracts\TagServiceInterface;
use App\Services\Params\Tag\CreateTagServiceParams;

class TagService extends BaseService implements TagServiceInterface
{
    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Busca uma tag pelo id
     *
     * @param string $tagId
     *
     * @return ServiceResponse
     */
    public function find(string $tagId): ServiceResponse
    {
        try {
            $tag = $this->tagRepository->findOrNull($tagId);
            if (is_null($tag)) {
                return new ServiceResponse(
                    true,
                    'Tag não encontrada.',
                    null,
                    [
                        new InternalError(
                            'Tag não encontrada.',
                            11
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('tagId'));
        }

        return new ServiceResponse(
            true,
            'Tag encontrada com sucesso.',
            $tag
        );
    }

    /**
     * Criação de uma nova tag
     *
     * @param CreateTagServiceParams $params
     *
     * @return ServiceResponse
     */
    public function store(CreateTagServiceParams $params): ServiceResponse
    {
        try {
            $tag = $this->tagRepository->create($params->toArray());
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('params'));
        }
        return new ServiceResponse(
            true,
            'Tag cadastrada com sucesso.',
            $tag
        );
    }

    /**
     * Realiza atualização da tag
     *
     * @param array $params
     * @param string $tagId
     *
     * @return ServiceResponse
     */
    public function update(array $params, string $tagId): ServiceResponse
    {
        try {
            $findTagResponse = $this->find($tagId);
            if (!$findTagResponse->success || is_null($findTagResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findTagResponse->message,
                    null,
                    $findTagResponse->internalErrors
                );
            }

            $tagUpdate = $this->tagRepository->update(
                $params,
                $tagId
            );
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('params', 'tagId'));
        }

        return new ServiceResponse(
            true,
            'Tag atualizada com sucesso.',
            $tagUpdate
        );
    }

    /**
     * Deleta uma tag pelo id
     *
     * @param string $tagId
     *
     * @return ServiceResponse
     */
    public function delete(string $tagId): ServiceResponse
    {
        try {
            $findTagResponse = $this->find($tagId);
            if (!$findTagResponse->success || is_null($findTagResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findTagResponse->message,
                    null,
                    $findTagResponse->internalErrors
                );
            }

            $this->tagRepository->delete($tagId);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('tagId'));
        }
        return new ServiceResponse(
            true,
            'Tag removida com sucesso.',
            null
        );
    }

    /**
     * Retorna todas as tags do usuário
     *
     * @param string $userId
     * @param string|null $description
     *
     * @return ServiceResponse
     */
    public function findAll(string $userId, string $description = null): ServiceResponse
    {
        try {
            $tags = $this->tagRepository->findAll($userId, $description);
            if (!count($tags)) {
                return new ServiceResponse(
                    true,
                    'Nenhuma tag encontrada para este usuário',
                    null,
                    [
                        new InternalError(
                            'Nenhuma tag encontrada para este usuário',
                            12
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return new $this->defaultErrorReturn($throwable, compact('userId'));
        }

        return new ServiceResponse(
            true,
            'Tags encontradas com sucesso!',
            $tags
        );
    }
}
