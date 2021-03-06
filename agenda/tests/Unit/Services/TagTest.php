<?php

namespace Tests\Unit\Services;

use App\Models\Tag;
use App\Models\User;
use App\Services\Responses\ServiceResponse;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Contracts\TagServiceInterface;
use App\Services\Params\Tag\CreateTagServiceParams;

class TagTest extends BaseTestCase
{
    /**
     * @var TagServiceInterface
     */
    protected $tagService;

    public function setUp(): void
    {
        parent::setUp();

        $this->tagService = app(TagServiceInterface::class);
    }

    /**
     * Testa o método Find na TagService retornando sucesso na busca
     * por uma tag, informando seu id e o id do usuário
     */
    public function testFindReturnSuccessWhenTagExists()
    {
        $tag = factory(Tag::class)->create();

        $findTagResponse = $this->tagService->find($tag->id, $tag->user_id);

        $this->assertInstanceOf(ServiceResponse::class, $findTagResponse);
        $this->assertNotNull($findTagResponse->data);
        $this->assertIsBool($findTagResponse->success);
        $this->assertTrue($findTagResponse->success);
    }

    /**
     * Testa o método Find na TagService retornando erro ao tentar
     * buscar uma tag que não existe
     */
    public function testFindReturnErrorWhenTagDoesntExists()
    {
        $tag = factory(Tag::class)->create();

        $tag->delete();

        $findTagResponse = $this->tagService->find($tag->id, $tag->user_id);

        $this->assertInstanceOf(ServiceResponse::class, $findTagResponse);
        $this->assertNull($findTagResponse->data);
        $this->assertIsBool($findTagResponse->success);
        $this->assertTrue($findTagResponse->success);
        $this->assertHasInternalError($findTagResponse, 11);
    }

    /**
     * Testa o método Find na TagService retornando erro ao tentar
     * buscar uma tag de um usuário que não existe ou tem id difente do logado
     */
    public function testReturnErrorWhenFindTagOtherUser()
    {
        $tag = factory(Tag::class)->create();

        $user = factory(User::class)->create();

        $findTagResponse = $this->tagService->find($tag->id, $user->id);

        $this->assertInstanceOf(ServiceResponse::class, $findTagResponse);
        $this->assertNull($findTagResponse->data);
        $this->assertNotFalse($findTagResponse->success);
        $this->assertHasInternalError($findTagResponse, 11);
    }

    /**
     * Testa o método Store na TagService retornando sucesso na criação
     * de uma nova tag para o usuário
     */
    public function testStoreReturnSuccessWhenCreateNewTag()
    {
        $user = factory(User::class)->create();

        $createTagResponse = $this->tagService->store(
            new CreateTagServiceParams(
                $this->faker->word,
                $user->id
            )
        );

        $this->assertInstanceOf(ServiceResponse::class, $createTagResponse);
        $this->assertTrue($createTagResponse->success);
        $this->assertNotNull($createTagResponse->data);
        $this->assertSame($user->id, $createTagResponse->data->user_id);
    }

    /**
     * Testa o método Update na TagService retornando sucesso ao realizar
     * a atualização do nome da tag do usuário
     */
    public function testUpdateSuccessWhenTagExists()
    {
        $tag = factory(Tag::class)->create();

        $array = ['description' => $this->faker->word];

        $updateTagResponse = $this->tagService->update(
            $array,
            $tag->id,
            $tag->user_id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateTagResponse);
        $this->assertNotFalse($updateTagResponse->success);
        $this->assertNotNull($updateTagResponse->data);
        $this->assertNotSame($tag->description, $updateTagResponse->data->description);
    }

    /**
     * Testa o método Update na TagService retornando erro ao tentar atualizar
     * uma tag que não existe
     */
    public function testUpdateReturnErrorWhenTagDoesntExists()
    {
        $tag = factory(Tag::class)->create();

        $tag->delete();

        $updateTagResponse = $this->tagService->update(
            [],
            $tag->id,
            $tag->user_id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateTagResponse);
        $this->assertIsBool($updateTagResponse->success);
        $this->assertFalse($updateTagResponse->success);
        $this->assertNull($updateTagResponse->data);
        $this->assertHasInternalError($updateTagResponse, 11);
    }

    /**
     * Testa o método Update na TagService retornando erro ao tentar atualizar
     * uma tag que pertence a outro usuário
     */
    public function testUpdateReturnErrorWhenTagOfOtherUser()
    {
        $tag = factory(Tag::class)->create();

        $user = factory(User::class)->create();

        $array = ['description' => $this->faker->word];

        $updateTagResponse = $this->tagService->update(
            $array,
            $tag->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateTagResponse);
        $this->assertNotTrue($updateTagResponse->success);
        $this->assertNull($updateTagResponse->data);
        $this->assertHasInternalError($updateTagResponse, 11);
        $this->assertIsArray($updateTagResponse->internalErrors);
    }

    /**
     * Testa o método Delete na TagService retornando sucesso ao
     * apagar uma tag vinculada ao usuário
     */
    public function testReturnSuccessWhenDeleteTag()
    {
        $tag = factory(Tag::class)->create();

        $deleteTagResponse = $this->tagService->delete(
            $tag->id,
            $tag->user_id
        );

        $this->assertInstanceOf(ServiceResponse::class, $deleteTagResponse);
        $this->assertNotFalse($deleteTagResponse->success);
        $this->assertNull($deleteTagResponse->data);
    }

    /**
     * Testa o método Delete na TagService retornando rrro ao tentar apagar
     * uma tag que a outro usuário
     */
    public function testReturnErrorWhenDeleteTagOfOtherUser()
    {
        $tag = factory(Tag::class)->create();

        $user = factory(User::class)->create();

        $deleteTagResponse = $this->tagService->delete(
            $tag->id,
            $user->id,
        );

        $this->assertInstanceOf(ServiceResponse::class, $deleteTagResponse);
        $this->assertFalse($deleteTagResponse->success);
        $this->assertNull($deleteTagResponse->data);
        $this->assertHasInternalError($deleteTagResponse, 11);
    }

    /**
     * Testa o método FindAll na TagService retornando sucesso ao buscar
     * todas as tags do usuário
     */
    public function testReturnSuccessWhenFindAllTagsUser()
    {
        $user = factory(User::class)->create();
        factory(Tag::class, 3)->create(['user_id' => $user->id]);

        $findAllTagsResponse = $this->tagService->findAll($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $findAllTagsResponse);
        $this->assertInstanceOf(Collection::class, $findAllTagsResponse->data);
        $this->assertNotNull($findAllTagsResponse->data);
        $this->assertTrue($findAllTagsResponse->success);
        $this->assertEquals(3, count($findAllTagsResponse->data));
    }

    /**
     * Testa o método FindAll na TagService retornando uma busca por uma tag
     * informando seu nome no parâmetro
     */
    public function testReturnSuccessWhenFindTagUserWithDescription()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create(['user_id' => $user->id]);

        $findAllTagsResponse = $this->tagService->findAll(
            $user->id,
            $tag->description
        );

        $data = $findAllTagsResponse->data->first();

        $this->assertInstanceOf(ServiceResponse::class, $findAllTagsResponse);
        $this->assertInstanceOf(Collection::class, $findAllTagsResponse->data);
        $this->assertNotNull($findAllTagsResponse->data);
        $this->assertTrue($findAllTagsResponse->success);
        $this->assertEquals($tag->description, $data->description);
    }

    /**
     * Testa o método FindAll na TagService retornando erro ao tentar buscar
     * tags de usário que não existe
     */
    public function testFindAllReturnErrorWhenUserDoesntExists()
    {
        $findAllTagsResponse = $this->tagService->findAll($this->faker->uuid);

        $this->assertInstanceOf(ServiceResponse::class, $findAllTagsResponse);
        $this->assertFalse($findAllTagsResponse->success);
        $this->assertNull($findAllTagsResponse->data);
        $this->assertHasInternalError($findAllTagsResponse, 3);
    }

    /**
     * Testa o método FindAll na TagService retornando sucesso ao listar as
     * tags de um usuário que ainda não possui tags cadastradas
     */
    public function testReturnSuccessWhenDoesntExistsTagToUser()
    {
        $user = factory(User::class)->create();

        $findAllTagsResponse = $this->tagService->findAll(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $findAllTagsResponse);
        $this->assertInstanceOf(Collection::class, $findAllTagsResponse->data);
        $this->assertEmpty($findAllTagsResponse->data);
        $this->assertTrue($findAllTagsResponse->success);
    }
}
