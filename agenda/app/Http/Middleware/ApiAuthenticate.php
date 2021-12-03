<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Traits\ResponseHelpers;
use App\Services\Responses\InternalError;
use App\Services\Contracts\AuthenticateTokenServiceInterface;

class ApiAuthenticate
{
    use ResponseHelpers;

    /**
     * @var AuthenticateTokenServiceInterface
     */
    private $authenticateTokenService;

    public function __construct()
    {
        $this->authenticateTokenService = app(AuthenticateTokenServiceInterface::class);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        $validationTokenResponse = $this->authenticateTokenService->findToken($token);
        if (
            !$validationTokenResponse->success || is_null($validationTokenResponse->data)
        ) {
            $error = new InternalError(
                'Token inválido!',
                6
            );

            return $this->unauthenticatedErrorResponse([$error]);
        }

        $authenticateToken = $validationTokenResponse->data;
        if ($authenticateToken->expires_at < Carbon::now()) {
            $error = new InternalError(
                'Token expirado!',
                7
            );

            return $this->unauthenticatedErrorResponse([$error]);
        }

        return $next($request);
    }
}
