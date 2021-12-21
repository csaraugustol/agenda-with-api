<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;
use App\Services\Params\Tag\CreateTagServiceParams;

interface TagServiceInterface
{
    public function find(string $tagId, string $userId): ServiceResponse;
    public function store(CreateTagServiceParams $params): ServiceResponse;
    public function update(array $params, string $tagId, string $userId): ServiceResponse;
    public function delete(string $tagId, string $userId): ServiceResponse;
    public function findAll(string $userId, string $description = null): ServiceResponse;
}
