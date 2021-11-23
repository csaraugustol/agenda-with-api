<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Services\Responses\InternalError;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            $response = new DefaultResponse(
                null,
                false,
                [],
                404
            );

            return response()->json($response->toArray(), 404);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  Request  $request
     * @param  ValidationException  $exception
     * @return JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        $errors = [];
        foreach ($exception->errors() as $fieldError) {
            foreach ($fieldError as $error) {
                $errors[] = new InternalError(
                    $error
                );
            }
        }

        $response = new DefaultResponse(
            null,
            false,
            $errors,
            422
        );

        return response()->json($response->toArray(), 422);
    }
}
