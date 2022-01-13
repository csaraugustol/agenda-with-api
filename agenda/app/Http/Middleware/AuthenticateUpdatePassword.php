<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Traits\ResponseHelpers;
use App\Services\Responses\InternalError;
use App\Services\Contracts\ChangePasswordServiceInterface;

class AuthenticateUpdatePassword
{
    use ResponseHelpers;

    /**
     * @var ChangePasswordServiceInterface
     */
    private $changePasswordService;

    public function __construct()
    {
        $this->changePasswordService = app(ChangePasswordServiceInterface::class);
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
        $token = $request->token_update_password;

        if (is_null($token)) {
            return $this->sendError(
                'O token de atualização não foi informado.',
                [],
                422
            );
        }

        $tokenToChangePasswordResponse = $this->changePasswordService->findByToken(
            $token,
            user('id')
        );

        if (
            !$tokenToChangePasswordResponse->success ||
            is_null($tokenToChangePasswordResponse->data)
        ) {
            $error = new InternalError(
                'O token informado é inválido!',
                18
            );

            return $this->unauthenticatedErrorResponse([$error]);
        }

        $tokenToChangePassword = $tokenToChangePasswordResponse->data;
        if ($tokenToChangePassword->expires_at < Carbon::now()) {
            $error = new InternalError(
                'O token foi expirado!',
                19
            );

            return $this->unauthenticatedErrorResponse([$error]);
        }

        return $next($request);
    }
}
