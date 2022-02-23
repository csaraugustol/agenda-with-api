<?php

namespace App\Services;

use Throwable;
use GuzzleHttp\Client;
use App\Models\ExternalToken;
use App\Services\Responses\InternalError;
use GuzzleHttp\Exception\RequestException;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\ContactServiceInterface;
use App\Services\Contracts\VexpensesServiceInterface;
use App\Services\Contracts\ExternalTokenServiceInterface;
use App\Services\Params\Contact\CreateCompleteContactsServiceParams;

class VexpensesService extends BaseService implements VexpensesServiceInterface
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $client = new Client([
            'base_uri' => config('auth.vexpenses_base_url'),
            'headers'  => [
                'Accept' => 'application/json',
            ]
        ]);

        $this->setClient($client);
    }

    /**
     * Envio de Requisição para a API VExpenses
     *
     * @param string $route
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function sendRequest(string $route, string $userId): ServiceResponse
    {
        try {
            $externalToken = $this->getToken($userId);

            if (is_null($externalToken)) {
                return new ServiceResponse(
                    false,
                    'Usuário não possui um token de integração com o VExpenses.',
                    null,
                    [
                        new InternalError(
                            'Usuário não possui um token de integração com o VExpenses.',
                            24
                        )
                    ]
                );
            }

            $options = [
                'headers' => [
                    'Authorization' => $externalToken->token,
                ]
            ];

            $response = $this->client->get($route, $options);

            $body = json_decode((string) $response->getBody());
        } catch (RequestException $requestError) {
            $responseCode = $requestError->getCode();

            if ($responseCode === 401) {
                return new ServiceResponse(
                    false,
                    'O token é inválido!',
                    null,
                    [
                        new InternalError(
                            'O token é inválido!',
                            25
                        )
                    ]
                );
            }

            if ($responseCode === 405) {
                return new ServiceResponse(
                    false,
                    'A rota informada não é válida!',
                    null,
                    [
                        new InternalError(
                            'A rota informada não é válida!',
                            26
                        )
                    ]
                );
            }

            return new ServiceResponse(
                false,
                'Requisição inválida.',
                null,
                [
                    new InternalError(
                        'Requisição inválida.',
                        27
                    )
                ]
            );
        }

        return new ServiceResponse(
            true,
            'Requisição realiza com sucesso.',
            $body->data
        );
    }

    /**
     * Seta o client
     *
     * @param Client $client
     *
     * @return void
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * Seta o token de integração com o VExpenses
     *
     * @param string $token
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function tokenToAccess(string $token, string $userId): ServiceResponse
    {
        try {
            $findUserResponse = app(UserServiceInterface::class)->find(
                $userId
            );
            if (!$findUserResponse->success || is_null($findUserResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findUserResponse->message,
                    null,
                    $findUserResponse->internalErrors
                );
            }

            $externalTokenResponse = app(ExternalTokenServiceInterface::class)
                ->storeToken($token, $userId, 'VEXPENSES');

            if (!$externalTokenResponse->success) {
                return $externalTokenResponse;
            }

            $token = $externalTokenResponse->data;
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('token', 'userId'));
        }

        return new ServiceResponse(
            true,
            'Token gerado com sucesso.',
            $token
        );
    }

    /**
     * Retorna ExternalToken para validar o acesso a integração
     *
     * @return string
     */
    private function getToken(string $userId): ?ExternalToken
    {
        $findExternalTokenResponse = app(ExternalTokenServiceInterface::class)->find(
            $userId,
            'VEXPENSES'
        );

        return $findExternalTokenResponse->data;
    }

    /**
     * Retorna todos os membros de equipe do Vexpenses
     *
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function findAllTeamMembers(string $userId): ServiceResponse
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

            $findMembersResponse = $this->sendRequest('team-members', $userId);

            if (!$findMembersResponse->success || is_null($findMembersResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findMembersResponse->message,
                    null,
                    $findMembersResponse->internalErrors
                );
            }

            $allMembers = collect($findMembersResponse->data);

            $filter = $allMembers->filter(function ($member) {
                return (!is_null($member->phone1) && $member->phone1 !== '' && $member->phone1 !== '(')
                    || (!is_null($member->phone2) && $member->phone2 !== '' && $member->phone2 !== '(');
            });

            //Retorna os dados relevantes do membro
            $members = $filter->map(function ($member) use ($userId) {

                $phones = collect();

                if ($member->phone1) {
                    $phones->push($member->phone1);
                }

                if ($member->phone2) {
                    $phones->push($member->phone2);
                }

                //Verifica se o membro já possui integração com algum contato
                $findExternalTokenResponse = app(ContactServiceInterface::class)
                    ->findContactByExternalId($userId, $member->id);

                return (object) [
                    'external_id' => $member->id,
                    'integrated'  => is_null($findExternalTokenResponse->data) ? false : true,
                    'name'        => $member->name,
                    'email'       => $member->email,
                    'phones'      => $phones
                ];
            });
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId'));
        }

        return new ServiceResponse(
            true,
            'Membros retornados com sucesso.',
            $members
        );
    }

    /**
     * Retorna dados de um membro de equipe do VExpenses
     *
     * @param string $userId
     * @param string $externalId
     *
     * @return ServiceResponse
     */
    public function findTeamMember(string $userId, string $externalId): ServiceResponse
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

            $findMemberResponse = $this->sendRequest('team-members/' . $externalId, $userId);

            if (!$findMemberResponse->success || is_null($findMemberResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findMemberResponse->message,
                    null,
                    $findMemberResponse->internalErrors
                );
            }

            //Data recebe o objeto membro
            $data = $findMemberResponse->data;

            //Verifica se o membro já possui integração com algum contato
            $findExternalTokenResponse = app(ContactServiceInterface::class)
                ->findContactByExternalId($userId, $data->id);

            $phones = collect();

            if ($data->phone1) {
                $phones->push($data->phone1);
            }

            if ($data->phone2) {
                $phones->push($data->phone2);
            }

            $member = (object) [
                'external_id' => $data->id,
                'integrated'  => is_null($findExternalTokenResponse->data) ? false : true,
                'name'        => $data->name,
                'email'       => $data->email,
                'phones'      => $phones
            ];
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('userId', 'externalId'));
        }

        return new ServiceResponse(
            true,
            'Membro retornado com sucesso.',
            $member
        );
    }

    /**
     * Cria um contato a partir de um mebro do VExpenses
     *
     * @param CreateCompleteContactsServiceParams $params
     *
     * @return ServiceResponse
     */
    public function store(CreateCompleteContactsServiceParams $params): ServiceResponse
    {
        try {
            $createContactResponse = app(ContactServiceInterface::class)->store($params);
            if (!$createContactResponse->success || is_null($createContactResponse->data)) {
                return new ServiceResponse(
                    false,
                    $createContactResponse->message,
                    null,
                    $createContactResponse->internalErrors
                );
            }

            $member = $createContactResponse->data;
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('params'));
        }

        return new ServiceResponse(
            true,
            'Membro retornado com sucesso.',
            $member
        );
    }
}
