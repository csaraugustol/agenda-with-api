<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\Address\UpdateRequest;
use App\Http\Resources\Address\AddressResource;
use App\Services\Contracts\AddressServiceInterface;

class AddressController extends ApiController
{
    /**
     * @var AddressServiceInterface
     */
    protected $addressService;

    /**
     * @param AddressServiceInterface $addressService
     */
    public function __construct(AddressServiceInterface $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Atualiza um endereço do contato
     *
     * PATCH /address/{id}
     *
     * @param UpdateRequest $request
     * @param string $contactId
     *
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, string $addressId): JsonResponse
    {
        $updateAddressResponse = $this->addressService->update(
            $request->toArray(),
            $addressId,
            user('id')
        );

        if (!$updateAddressResponse->success || is_null($updateAddressResponse->data)) {
            return $this->errorResponseFromService($updateAddressResponse);
        }

        return $this->response(new DefaultResponse(
            new AddressResource($updateAddressResponse->data)
        ));
    }

    /**
     * Deleta um endereço do contato
     *
     * DELETE /address/{id}
     *
     * @param string $addressId
     *
     * @return JsonResponse
     */
    public function delete(string $addressId): JsonResponse
    {
        $deleteAddressResponse = $this->addressService->delete(
            $addressId,
            user('id')
        );

        if (!$deleteAddressResponse->success) {
            return $this->errorResponseFromService($deleteAddressResponse);
        }

        return $this->response(new DefaultResponse());
    }
}
