<?php

namespace App\Http\Controllers;

use App\Traits\ResponseHelpers;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ApiController extends Controller
{
    use ResponseHelpers;
    use ValidatesRequests;

    /**
     * Helper para ser usado na resposta de todas as controllers filhas
     *
     * @param  DefaultResponse $response
     *
     * @return JsonResponse
     */
    public function response(DefaultResponse $response): JsonResponse
    {
        return response()->json($response->toArray(), $response->code);
    }

    /**
     * Helper para montar response de error a partir de um ServiceResponse
     *
     * @param  ServiceResponse $serviceResponse
     *
     * @return JsonResponse
     */
    public function errorResponseFromService(ServiceResponse $serviceResponse): JsonResponse
    {
        $errors = $serviceResponse->internalErrors;
        if (!count($errors)) {
            $errors = [
                new InternalError(
                    $serviceResponse->message,
                    null,
                    null
                )
            ];
        }

        return $this->response(new DefaultResponse(
            null,
            false,
            $errors,
            count($serviceResponse->internalErrors) ? 200 : 500
        ));
    }
}
