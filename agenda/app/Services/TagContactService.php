<?php

namespace App\Services;

use Throwable;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\TagServiceInterface;
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
     *
     * @return ServiceResponse
     */
    public function attach(string $tagId, string $contactId): ServiceResponse
    {
        try {
            $findTagResponse = app(TagServiceInterface::class)->find(
                $tagId
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
                $contactId
            );
            if (!$findContactResponse->success || is_null($findContactResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findContactResponse->message,
                    null,
                    $findContactResponse->internalErrors
                );
            }

            $tag = $findTagResponse->data;
            $contact = $findContactResponse->data;

            if ($tag->user_id !== $contact->user_id) {
                return new ServiceResponse(
                    false,
                    'Não é possível realizar a vinculação.',
                    null,
                    [
                        new InternalError(
                            'Não é possível realizar a vinculação.',
                            12
                        )
                    ]
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

            if (!is_null($tagContact->deleted_at)) {
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
            return $this->defaultErrorReturn($throwable, compact('tagId', 'contactId'));
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
     *
     * @return ServiceResponse
     */
    public function dettach(string $tagId, string $contactId): ServiceResponse
    {
        try {
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
