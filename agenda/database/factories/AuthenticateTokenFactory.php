<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;
use App\Models\User;
use App\Models\AuthenticateToken;
use Illuminate\Support\Facades\Hash;

$factory->define(AuthenticateToken::class, function () {
    return [
        'token'      => Hash::make(Carbon::now() . bin2hex(random_bytes(17))),
        'expires_at' => Carbon::now()->addMinutes(config('auth.time_to_expire_update_password')),
        'user_id'    => factory(User::class),
    ];
});
