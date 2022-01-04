<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Traits\ResponseHelpers;
use App\Services\Responses\InternalError;
use App\Services\Contracts\ChangePasswordServiceInterface;

class TokenToUpdatePassword
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

        $changePassword = $tokenToChangePasswordResponse->data;
        if ($changePassword->expires_at < Carbon::now()) {
            $error = new InternalError(
                'O token foi expirado!',
                19
            );

            return $this->unauthenticatedErrorResponse([$error]);
        }

        return $next($request);
    }
}
