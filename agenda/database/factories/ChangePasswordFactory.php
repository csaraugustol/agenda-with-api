<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;
use App\Models\User;
use Faker\Generator as Faker;
use App\Models\ChangePassword;
use Illuminate\Support\Facades\Hash;

$factory->define(ChangePassword::class, function (Faker $faker) {
    return [
        'user_id'    => factory(User::class),
        'token'      => Hash::make(Carbon::now() . bin2hex(random_bytes(17))),
        'expires_at' => Carbon::now()->addMinutes(config('auth.time_to_expire_update_password')),
    ];
});
