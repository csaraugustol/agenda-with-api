<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\AuthenticateToken;

$factory->define(AuthenticateToken::class, function (Faker $faker) {
    return [
        'token'      => $faker->md5,
        'expires_at' => new DateTime(),
        'user_id'    => factory(User::class),
    ];
});
