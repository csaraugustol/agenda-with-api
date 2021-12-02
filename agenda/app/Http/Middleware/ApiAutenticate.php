<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Traits\ResponseHelpers;
use App\Services\Responses\InternalError;
use App\Services\Contracts\AuthenticateTokenServiceInterface;

class ApiAutenticate
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
        $token = '$2y$10$lUtgopsy7dCL.7W7OqT2AetVi1/H/st.T.tE4xUUuLQ1ZpGwiAYrW';

        $validationTokenResponse = $this->authenticateTokenService->validateToken($token);

        $authenticateToken = $validationTokenResponse->data;

        if ($authenticateToken['token'] !== $token || $authenticateToken['expires_at'] > Carbon::now()) {
            $error = new InternalError(
                'Token inválido!',
                6
            );

            return $this->unauthenticatedErrorResponse([$error]);
        }

        return $next($request);
    }
}
