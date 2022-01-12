<?php

namespace Tests\Unit\Services;

use App\Models\Phone;
use App\Models\Contact;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\PhoneServiceInterface;
use App\Services\Params\Phone\CreatePhoneServiceParams;

class PhoneTest extends BaseTestCase
{
    /**
     * @var PhoneServiceInterface
     */
    protected $phoneService;

    public function setUp(): void
    {
        parent::setUp();

        $this->phoneService = app(PhoneServiceInterface::class);
    }

    /**
     * Testa o método Find na PhoneService retornando sucesso na busca do id
     * do telefone informado
     */
    public function testFindReturnSuccessWhenExistsPhone()
    {
        $phone = factory(Phone::class)->create();

        $findPhoneResponse = $this->phoneService->find($phone->id);

        $this->assertInstanceOf(ServiceResponse::class, $findPhoneResponse);
        $this->assertTrue($findPhoneResponse->success);
        $this->assertNotNull($findPhoneResponse->data);
        $this->assertEquals($phone->id, $findPhoneResponse->data->id);
    }

    /**
     * Testa o método Find na PhoneService forçando um erro com um id fake
     * para verificar se a resposta de erro está correta
     */
    public function testeFindReturnErrorWhenPhoneDoesntExists()
    {
        $findPhoneResponse = $this->phoneService->find($this->faker->uuid);

        $this->assertInstanceOf(ServiceResponse::class, $findPhoneResponse);
        $this->assertNotFalse($findPhoneResponse->success);
        $this->assertNull($findPhoneResponse->data);
        $this->assertHasInternalError($findPhoneResponse, 10);
    }

    /**
     * Testa o método Store na PhoneService para verificar a criação com
     * sucesso de um novo número de telefone para o contato do usuário
     */
    public function testStoreReturnSuccessWhenCreateNewPhone()
    {
        $contact = factory(Contact::class)->create();

        $createPhoneResponse = $this->phoneService->store(
            new CreatePhoneServiceParams(
                $this->faker->phoneNumber,
                $contact->id
            )
        );

        $this->assertInstanceOf(ServiceResponse::class, $createPhoneResponse);
        $this->assertTrue($createPhoneResponse->success);
        $this->assertNotNull($createPhoneResponse->data);
        $this->assertEquals($contact->id, $createPhoneResponse->data->contact->id);
    }

    /**
     * Testa o método Update na PhoneService retornando sucesso na atualização
     * de um número de telefone
     */
    public function testUpdateReturnSuccessWhenExistsPhone()
    {
        $phone = factory(Phone::class)->create();

        $array = ['phone_number' => $this->faker->phoneNumber];

        $updatePhoneResponse = $this->phoneService->update(
            $array,
            $phone->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updatePhoneResponse);
        $this->assertNotFalse($updatePhoneResponse->success);
        $this->assertNotNull($updatePhoneResponse->data);
        $this->assertNotEquals($phone->phone_number, $updatePhoneResponse->data->phone_number);
    }

    /**
     * Testa o método Update na PhoneService retornando uma falha ao informar um
     * id de um telefone que não existe
     */
    public function testUpdateReturnErrorWhenPhoneDoesntExists()
    {
        $array = ['phone_number' => $this->faker->phoneNumber];

        $updatePhoneResponse = $this->phoneService->update(
            $array,
            $this->faker->uuid
        );

        $this->assertInstanceOf(ServiceResponse::class, $updatePhoneResponse);
        $this->assertNotTrue($updatePhoneResponse->success);
        $this->assertNull($updatePhoneResponse->data);
        $this->assertHasInternalError($updatePhoneResponse, 10);
    }

    /**
     * Testa o método Delete na PhoneService retornando sucesso ao
     * realizar a deleção do telefone de id fornecido
     */
    public function testDeleteReturnSuccessWhenExistsPhone()
    {
        $phone = factory(Phone::class)->create();

        $deletePhoneResponse = $this->phoneService->delete($phone->id);

        $this->assertInstanceOf(ServiceResponse::class, $deletePhoneResponse);
        $this->assertTrue($deletePhoneResponse->success);
        $this->assertNull($deletePhoneResponse->data);
    }

    /**
     * Testa o método Delete na PhoneService retornando a resposta de erro
     * ao informar um id de um telefone que não existe
     */
    public function testDeleteReturnErrorWhenPhoneDoesntExists()
    {
        $deletePhoneResponse = $this->phoneService->delete($this->faker->uuid);

        $this->assertInstanceOf(ServiceResponse::class, $deletePhoneResponse);
        $this->assertFalse($deletePhoneResponse->success);
        $this->assertIsBool($deletePhoneResponse->success);
        $this->assertNull($deletePhoneResponse->data);
        $this->assertHasInternalError($deletePhoneResponse, 10);
    }
}
