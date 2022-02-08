<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;
use App\Models\User;
use App\Models\ExternalToken;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

$factory->define(ExternalToken::class, function (Faker $faker) {
    return [
        'token'      => Hash::make(Carbon::now() . bin2hex(random_bytes(17))),
        'expires_at' => null,
        'system'     => $faker->randomElement(['VEXPENSES']), //Caso adicionar mais integração, só colocar no array
        'user_id'    => factory(User::class),
    ];
});
