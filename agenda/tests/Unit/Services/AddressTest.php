<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Address;
use App\Models\Contact;
use App\Services\ViaCepService;
use Tests\Mocks\Providers\ViaCepProvider;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\AddressServiceInterface;
use App\Services\Params\Address\CreateAddressServiceParams;

class AddressServiceTest extends BaseTestCase
{
    /**
     * @var AddressServiceInterface
     */
    protected $addressService;

    /**
     * @var ViaCepProvider
     */
    protected $viaCepProvider;

    public function setUp(): void
    {
        parent::setUp();

        $this->addressService = app(AddressServiceInterface::class);
        $this->viaCepProvider = app(ViaCepProvider::class);
    }

    /**
     * Testa o método find na service de Endereço para encontrar
     * um endereço pelo id e executar sucesso
     */
    public function testReturnSuccessWhenAddressExists()
    {
        $addressFactory = factory(Address::class)->create();

        $findAddressResponse = $this->addressService->find($addressFactory->id);

        $address = $findAddressResponse->data;

        $this->assertInstanceOf(ServiceResponse::class, $findAddressResponse);
        $this->assertNotNull($address);
        $this->assertTrue($findAddressResponse->success);
        $this->assertEquals($addressFactory->id, $address->id);
    }

    /**
     * Testa o método find na service de Endereço que deve gerar uma falha
     * na busca de um endereço pelo id que não existe
     */
    public function testFindAddressReturnErrorWhenAddressDoesntExists()
    {
        $findAddressResponse = $this->addressService->find($this->faker->uuid);

        $address = $findAddressResponse->data;

        $this->assertInstanceOf(ServiceResponse::class, $findAddressResponse);
        $this->assertNull($address);
        $this->assertTrue($findAddressResponse->success);
        $this->assertHasInternalError($findAddressResponse, 9);
    }

    /**
     * Testa o método store na service de Endereço para a criação de um novo
     * endereço para o contato
     */
    public function testStoreAddressWithSuccess()
    {
        $contact = factory(Contact::class)->create();

        $createAddressResponse = $this->addressService->store(
            new CreateAddressServiceParams(
                $this->faker->streetName,
                $this->faker->buildingNumber,
                $this->faker->secondaryAddress,
                $this->faker->streetSuffix,
                $this->faker->city,
                $this->faker->state,
                $this->faker->postcode,
                $this->faker->country,
                $contact->id,
            )
        );

        $this->assertInstanceOf(ServiceResponse::class, $createAddressResponse);
        $this->assertTrue($createAddressResponse->success);
        $this->assertNotNull($createAddressResponse->data);
    }

    /**
     * Testa o método update na service de Endereço para atualizar dados
     * de um endereço para o contato do usuário
     */
    public function testUpdateSuccessWhenAddressExists()
    {
        $address = factory(Address::class)->create();

        $array = ['number' => $this->faker->buildingNumber];

        $updateAddressReponse = $this->addressService->update(
            $array,
            $address->id,
            $address->contact->user_id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateAddressReponse);
        $this->assertTrue($updateAddressReponse->success);
        $this->assertNotNull($updateAddressReponse->data);
        $this->assertEquals($updateAddressReponse->data->number, $array['number']);
    }

    /**
     * Testa o método update na service de Endereço para verificar erro na passagem
     * dos argumentos para atualizar dados de um endereço para o contato do usuário
     */
    public function testUpdateReturnErrorWhenAddressDoesntExists()
    {
        $address = factory(Address::class)->create();

        $address->delete();

        $updateAddressReponse = $this->addressService->update(
            [],
            $address->id,
            $address->contact->user_id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateAddressReponse);
        $this->assertNotTrue($updateAddressReponse->success);
        $this->assertIsBool($updateAddressReponse->success);
        $this->assertNull($updateAddressReponse->data);
        $this->assertHasInternalError($updateAddressReponse, 9);
    }

    /**
     * Testa o método update na service de Endereço para verificar erro ao tentar
     * atualizar um endereço de outro usuário
     */
    public function testUpdateReturnErrorWhenAddressDoesntOfUser()
    {
        $address = factory(Address::class)->create();

        $user = factory(User::class)->create();

        $array   = ['city' => $this->faker->city];

        $updateAddressReponse = $this->addressService->update(
            $array,
            $address->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateAddressReponse);
        $this->assertFalse($updateAddressReponse->success);
        $this->assertIsBool($updateAddressReponse->success);
        $this->assertNull($updateAddressReponse->data);
        $this->assertHasInternalError($updateAddressReponse, 15);
        $this->assertIsArray($updateAddressReponse->internalErrors);
    }

    /**
     * Testa o método delete na service de Endereço para deletar um endereço
     * vinculado a um contato do usuário, passando o id do endereço e o
     * id do usuário e retorna sucesso
     */
    public function testReturnSuccessToDeleteAddressContact()
    {
        $address = factory(Address::class)->create();

        $deleteAddressResponse = $this->addressService->delete(
            $address->id,
            $address->contact->user_id
        );

        $this->assertInstanceOf(ServiceResponse::class, $deleteAddressResponse);
        $this->assertTrue($deleteAddressResponse->success);
        $this->assertNull($deleteAddressResponse->data);
    }

    /**
     * Testa o método delete na service de Endereço para testar erro ao deletar
     * um endereço vinculado a um contato do usuário passando um id inexistente
     */
    public function testReturnErrorWhenAddressToDeleteDoesntExists()
    {
        $address = factory(Address::class)->create();

        $deleteAddressResponse = $this->addressService->delete(
            $this->faker->uuid,
            $address->contact->user_id
        );

        $this->assertInstanceOf(ServiceResponse::class, $deleteAddressResponse);
        $this->assertNotTrue($deleteAddressResponse->success);
        $this->assertNull($deleteAddressResponse->data);
        $this->assertHasInternalError($deleteAddressResponse, 9);
    }

    /**
     * Testa o método delete na service de Endereço para testar erro ao deletar
     * um endereço vinculado a um contato do usuário, passando o id do endereço e o
     * id de um usuário diferente do usuário que está tentado deletar
     */
    public function testReturnErrorWhenDeleteAddressOtherUser()
    {
        $address = factory(Address::class)->create();

        $deleteAddressResponse = $this->addressService->delete(
            $address->id,
            $this->faker->uuid
        );

        $this->assertInstanceOf(ServiceResponse::class, $deleteAddressResponse);
        $this->assertFalse($deleteAddressResponse->success);
        $this->assertNull($deleteAddressResponse->data);
        $this->assertHasInternalError($deleteAddressResponse, 15);
    }

    /**
     * Retorna sucesso ao tentar realizar a busca de informações de um CEP
     */
    public function testReturnSuccessWhenFindByAddressOfPostalCode()
    {
        $postalCode = $this->faker->regexify('[0-9]{8}');

        $mockPostalCodeResponse = $this->viaCepProvider->getMockPostalCode(
            $postalCode
        );

        $this->addMockMethod(
            'sendRequestViaCep',
            new ServiceResponse(
                true,
                '',
                $mockPostalCodeResponse->response
            )
        );

        $this->applyMock(ViaCepService::class);

        $findAddressResponse = $this->addressService->findByPostalCode(
            $mockPostalCodeResponse->response->cep
        );

        $this->assertInstanceOf(ServiceResponse::class, $findAddressResponse);
        $this->assertTrue($findAddressResponse->success);
        $this->assertNotNull($findAddressResponse->data);
        $this->assertEquals($mockPostalCodeResponse->response->uf, $findAddressResponse->data->uf);
    }


    /**
     * Retorna erro ao tentar realizar a busca de informações de um CEP que não
     * existe
     */
    public function testReturErrorWhenFindByAddressOfPostalCode()
    {
        $mockPostalCodeResponse = $this->viaCepProvider->getMockResponseErrorAPIViaCep();

        $ViaCepService = app(ViaCepService::class);

        $this->viaCepProvider->setMockRequest(
            $ViaCepService,
            $mockPostalCodeResponse->status_code,
            $mockPostalCodeResponse->response,
        );

        $findAddressResponse = $this->addressService->findByPostalCode(
            $this->faker->regexify('[0-9]{8}')
        );

        $this->assertInstanceOf(ServiceResponse::class, $findAddressResponse);
        $this->assertHasInternalError($findAddressResponse, 16);
        $this->assertFalse($findAddressResponse->success);
        $this->assertNull($findAddressResponse->data);
    }

    /**
     * Retorna erro ao tentar realizar a busca de informações de um CEP que possui
     * um formato inválido
     */
    public function testReturErrorWhenFindByInvalidFormatPostalCode()
    {
        $mockPostalCodeResponse = $this->viaCepProvider->getMockResponseWhenRequestError();

        $ViaCepService = app(ViaCepService::class);

        $this->viaCepProvider->setMockRequest(
            $ViaCepService,
            $mockPostalCodeResponse->status_code,
            $mockPostalCodeResponse->response,
        );

        $findAddressResponse = $this->addressService->findByPostalCode(
            $this->faker->regexify('[0-7]{7}')
        );

        $this->assertInstanceOf(ServiceResponse::class, $findAddressResponse);
        $this->assertFalse($findAddressResponse->success);
        $this->assertNull($findAddressResponse->data);
        $this->assertHasInternalError($findAddressResponse, 16);
    }
}
