<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\TagRepository;

/**
 * Class TagRepositoryEloquent
 * @package namespace App\Repositories;
 */
class TagRepositoryEloquent extends BaseRepositoryEloquent implements TagRepository
{
    /**
     * Retorna nome da model
     *
     * @return string
     */
    public function model()
    {
        return Tag::class;
    }

    /**
     * Retorna todas as tags do usuário
     *
     * @param string $userId
     * @param string|null $description
     *
     * @return Collection
     */
    public function findAll(string $userId, string $description = null): Collection
    {
        $query =  $this->model
            ->where('user_id', $userId);
        if ($description) {
            $query->where('description', 'like', '%' . $description . '%');
        }

        return $query->get();
    }

    /**
     * Busca uma tag pelo id
     * e usuário logado
     *
     * @param string $tagId
     * @param string $userId
     *
     * @return Tag|null
     */
    public function findTagByUserId(string $tagId, string $userId): ?Tag
    {
        return $this->model
            ->where('id', $tagId)
            ->where('user_id', $userId)
            ->first();
    }
}
