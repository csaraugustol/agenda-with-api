<?php

namespace App\Services;

use Throwable;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\TagServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\ContactServiceInterface;
use App\Repositories\Contracts\TagContactRepository;
use App\Services\Contracts\TagContactServiceInterface;

class TagContactService extends BaseService implements TagContactServiceInterface
{
    /**
     * @var TagContactRepository
     */
    private $tagContactRepository;

    /**
     * @param TagContactRepository $tagContactRepository
     */
    public function __construct(TagContactRepository $tagContactRepository)
    {
        $this->tagContactRepository = $tagContactRepository;
    }

    /**
     * Cria uma vinculação entre a tag e o contato
     *
     * @param string $tagId
     * @param string $contactId
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function attach(string $tagId, string $contactId, string $userId): ServiceResponse
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

            $findTagResponse = app(TagServiceInterface::class)->find(
                $tagId,
                $userId
            );
            if (!$findTagResponse->success || is_null($findTagResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findTagResponse->message,
                    null,
                    $findTagResponse->internalErrors
                );
            }

            $findContactResponse = app(ContactServiceInterface::class)->find(
                $contactId,
                $userId
            );
            if (!$findContactResponse->success || is_null($findContactResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findContactResponse->message,
                    null,
                    $findContactResponse->internalErrors
                );
            }

            //Verifica se existe vinculo
            $tagContact = $this->tagContactRepository->findTagContact(
                $tagId,
                $contactId
            );

            if ($tagContact) {
                return new ServiceResponse(
                    true,
                    'Vinculação realizada com sucesso.',
                    $tagContact
                );
            }

            //Verifica se existe vinculo deletado para restaurar
            $tagContact = $this->tagContactRepository->findTagContact(
                $tagId,
                $contactId,
                true
            );

            if (!is_null($tagContact)) {
                $tagContact->restore();
                return new ServiceResponse(
                    true,
                    'Vinculação realizada com sucesso.',
                    $tagContact
                );
            }

            //Cria um novo vínculo caso não encontre as condições
            $tagContact = $this->tagContactRepository->create([
                'tag_id'     => $tagId,
                'contact_id' => $contactId
            ]);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('tagId', 'contactId', 'userId'));
        }

        return new ServiceResponse(
            true,
            'Vinculação realizada com sucesso.',
            $tagContact
        );
    }

    /**
     * Busca por uma TagContact e deleta a vinculação entre a tag e o contato
     *
     * @param string $tagId
     * @param string $contactId
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function detach(string $tagId, string $contactId, string $userId): ServiceResponse
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

            $findTagResponse = app(TagServiceInterface::class)->find(
                $tagId,
                $userId
            );
            if (!$findTagResponse->success || is_null($findTagResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findTagResponse->message,
                    null,
                    $findTagResponse->internalErrors
                );
            }

            $findContactResponse = app(ContactServiceInterface::class)->find(
                $contactId,
                $userId
            );
            if (!$findContactResponse->success || is_null($findContactResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findContactResponse->message,
                    null,
                    $findContactResponse->internalErrors
                );
            }

            $tagContact = $this->tagContactRepository->findTagContact(
                $tagId,
                $contactId
            );

            if (!is_null($tagContact)) {
                $tagContact->delete();
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('tagId', 'contactId'));
        }

        return new ServiceResponse(
            true,
            'Vinculação desfeita com sucesso.',
            null
        );
    }
}
