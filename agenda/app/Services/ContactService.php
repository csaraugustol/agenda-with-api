<?php

namespace App\Services;

use Throwable;
use Illuminate\Support\Facades\DB;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\UserServiceInterface;
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
     * Retorna um contato pelo id
     *
     * @param string $contactId
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function find(string $contactId, string $userId): ServiceResponse
    {
        try {
            $findUserResponse = app(UserServiceInterface::class)->find($userId);
            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            $contact = $this->contactRepository->findContactByUserId(
                $contactId,
                $userId
            );

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
            $findUserResponse = app(UserServiceInterface::class)->find($params->user_id);
            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            $contact = $this->contactRepository->create([
                'name'    => $params->name,
                'user_id' => $params->user_id
            ]);

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
                    ->attach($tag['id'], $contact->id, $params->user_id);

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

    /**
     * Atualiza nome do contato do usuário
     *
     * @param string $contactName
     * @param string $contactId
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function update(string $contactName, string $contactId, string $userId): ServiceResponse
    {
        try {
            $findContactResponse = $this->find($contactId, $userId);

            if (!$findContactResponse->success || is_null($findContactResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findContactResponse->message,
                    null,
                    $findContactResponse->internalErrors
                );
            }

            $contactUpdate = $this->contactRepository->update([
                'name' => $contactName
            ], $contactId);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('contactName', 'contactId'));
        }

        return new ServiceResponse(
            true,
            'Contato atualizado com sucesso.',
            $contactUpdate
        );
    }

    /**
     * Deleta um contato
     *
     * @param string $contactId
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function delete(string $contactId, string $userId): ServiceResponse
    {
        try {
            $findContactResponse = $this->find($contactId, $userId);

            if (!$findContactResponse->success || is_null($findContactResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findContactResponse->message,
                    null,
                    $findContactResponse->internalErrors
                );
            }

            $this->contactRepository->delete($contactId);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('contactId', 'userId'));
        }

        return new ServiceResponse(
            true,
            'Contato removido com sucesso.',
            null
        );
    }

    /**
     * Busca por um contato que possui external_id
     *
     * @param string $userId
     * @param string $externalId
     *
     * @return ServiceResponse
     */
    public function findContactByExternalId(string $userId, string $externalId): ServiceResponse
    {
        try {
            $findUserResponse = app(UserServiceInterface::class)->find($userId);
            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            $contact = $this->contactRepository->findContactByExternalId(
                $userId,
                $externalId
            );

            if (is_null($contact)) {
                return new ServiceResponse(
                    true,
                    'O contato com um id externo não foi localizado.',
                    null,
                    [
                        new InternalError(
                            'O contato com um id externo não foi localizado.',
                            29
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId', 'externalId'));
        }

        return new ServiceResponse(
            true,
            "Contato localizado com sucesso!",
            $contact
        );
    }
}
