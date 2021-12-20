<?php

namespace App\Services;

use Throwable;
use Illuminate\Support\Facades\DB;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\ContactRepository;
use App\Services\Contracts\PhoneServiceInterface;
use App\Services\Contracts\AddressServiceInterface;
use App\Services\Contracts\ContactServiceInterface;
use App\Services\Contracts\TagContactServiceInterface;
use App\Services\Params\Phone\CreatePhoneServiceParams;
use App\Services\Params\Address\CreateAddressServiceParams;
use App\Services\Params\Contact\CreateCompleteContactsServiceParams;

class ContactService extends BaseService implements ContactServiceInterface
{
    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * @param ContactRepository $contactRepository
     */
    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    /**
     * Busca todos os contatos do usuário podendo ser
     * usada a filtragem
     *
     * @param string      $userId
     * @param string|null $filter
     *
     * @return ServiceResponse
     */
    public function findAllWithFilter(string $userId, string $filter = null): ServiceResponse
    {
        try {
            $contacts = $this->contactRepository->findAllWithFilter($userId, $filter);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId', 'filters'));
        }

        return new ServiceResponse(
            true,
            "Busca aos contatos realizada com sucesso.",
            $contacts
        );
    }

    /**
     * Retorna um contato do usuário pelo id
     * para mostrar seus detalhes
     *
     * @param string $userId
     * @param string $contactId
     *
     * @return ServiceResponse
     */
    public function findByUserContact(string $userId, string $contactId): ServiceResponse
    {
        try {
            $findContactResponse = $this->find($contactId);

            if (!$findContactResponse->success || is_null($findContactResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findContactResponse->message,
                    null,
                    $findContactResponse->internalErrors
                );
            }

            $contact = $findContactResponse->data;
            if ($contact->user_id !== $userId) {
                return new ServiceResponse(
                    false,
                    "Contato não foi localizado na sua listagem!",
                    null,
                    [
                        new InternalError(
                            'Contato não foi localizado na sua listagem!',
                            15
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId', 'contactId'));
        }

        return new ServiceResponse(
            true,
            "Busca realizada com sucesso!",
            $contact
        );
    }

    /**
     * Retorna um contato pelo id
     *
     * @param string $contactId
     *
     * @return ServiceResponse
     */
    public function find(string $contactId): ServiceResponse
    {
        try {
            $contact = $this->contactRepository->findOrNull($contactId);

            if (is_null($contact)) {
                return new ServiceResponse(
                    true,
                    'O contato não foi localizado.',
                    null,
                    [
                        new InternalError(
                            'O contato não foi localizado.',
                            14
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('contactId'));
        }

        return new ServiceResponse(
            true,
            "Contato encontrado com sucesso!",
            $contact
        );
    }

    /**
     * Verifica se já existe cadastrado para o usuário
     * um contato com o nome fornecido
     *
     * @param string $contactName
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function verifyExistsContactNameRegisteredUser(string $contactName, string $userId): ServiceResponse
    {
        try {
            $countContactName = $this->contactRepository->verifyExistsContactNameRegisteredUser(
                $contactName,
                $userId
            );
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('contactName', 'userId'));
        }

        return new ServiceResponse(
            true,
            "Contagem do contato realizada com sucesso.",
            $countContactName
        );
    }

    /**
     * Cria um contato completo
     *
     * @param CreateCompleteContactsServiceParams $params
     *
     * @return ServiceResponse
     */
    public function store(CreateCompleteContactsServiceParams $params): ServiceResponse
    {
        DB::beginTransaction();
        try {
            $contact = $this->contactRepository->create([
                'name'    => $params->name,
                'user_id' => $params->user_id
            ]);

            if (is_null($contact)) {
                DB::rollback();
                return $contact;
            }

            //Cria o telefone relacionado ao contato
            foreach ($params->phones as $phone) {
                $createPhoneParams = new CreatePhoneServiceParams(
                    $phone['phone_number'],
                    $contact->id
                );

                $createPhoneResponse = app(PhoneServiceInterface::class)
                    ->store($createPhoneParams);

                if (!$createPhoneResponse->success) {
                    DB::rollback();
                    return $createPhoneResponse;
                }
            }

            //Cria o endereço relacionado ao contato
            foreach ($params->adresses as $address) {
                $createAddressParams = new CreateAddressServiceParams(
                    $address['street_name'],
                    $address['number'],
                    $address['complement'],
                    $address['neighborhood'],
                    $address['city'],
                    $address['state'],
                    $address['postal_code'],
                    $address['country'],
                    $contact->id
                );

                $createAddressResponse = app(AddressServiceInterface::class)
                    ->store($createAddressParams);

                if (!$createAddressResponse->success) {
                    DB::rollBack();
                    return $createAddressResponse;
                }
            }

            //Cria o relacionamento da tag com o contato
            foreach ($params->tags as $tag) {
                $createTagContactResponse = app(TagContactServiceInterface::class)
                    ->attach($tag['id'], $contact->id);

                if (!$createTagContactResponse->success) {
                    DB::rollBack();
                    return $createTagContactResponse;
                }
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('params'));
        }

        DB::commit();

        return new ServiceResponse(
            true,
            "Contato criado com sucesso.",
            $contact
        );
    }
}
