<?php

namespace App\Services;

use Throwable;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\TagServiceInterface;
use App\Repositories\Contracts\TagContactRepository;
use App\Services\Contracts\TagContactServiceInterface;
use App\Services\Params\TagContact\CreateTagContactServiceParams;

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
     * @param CreateTagContactServiceParams $params
     *
     * @return ServiceResponse
     */
    public function attach(CreateTagContactServiceParams $params): ServiceResponse
    {
        try {
            $tagService = app(TagServiceInterface::class);
            $contactService = app(ContactServiceInterface::class);

            $findTagResponse = $tagService->find($params->tag_id);
            if (!$findTagResponse->success) {
                return $findTagResponse;
            }

            $findContactResponse = $contactService->find($params->contact_id);
            if (!$findContactResponse->success) {
                return $findContactResponse;
            }

            $tag = $findTagResponse->data;
            $contact = $findContactResponse->data;

            if ($tag->user_id === user('id') && $contact->user_id === user('id')) {
                return new ServiceResponse(
                    true,
                    'Já existe a vinculação ativa.',
                    null,
                    [
                        new InternalError(
                            'Já existe a vinculação ativa.',
                            12
                        )
                    ]
                );
            }

            $tagContact = $this->tagContactRepository->verifyExistsDeletedAttach(
                $tag->user_id,
                $contact->user_id
            );

            if ($tagContact) {
                $tagContact->restore();
            }

            $newTagContact = $this->tagContactRepository->create($params->toArray());
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('params'));
        }

        return new ServiceResponse(
            true,
            'Vinculação realizada com sucesso.',
            $newTagContact
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
            $tagService = app(TagServiceInterface::class);
            $contactService = app(ContactServiceInterface::class);

            $findTagResponse = $tagService->find($tagId);
            if (!$findTagResponse->success) {
                return $findTagResponse;
            }

            $findContactResponse = $contactService->find($contactId);
            if (!$findContactResponse->success) {
                return $findContactResponse;
            }

            $tagContact = $this->tagContactRepository->findTagContact(
                $tagId,
                $contactId
            );

            $tagContact->delete();
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('tagId', 'contactId'));
        }

        return new ServiceResponse(
            true,
            'Vicunlação desfeita com sucesso.',
            null
        );
    }
}
