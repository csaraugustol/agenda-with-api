<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Phone;
use App\Models\Contact;
use Faker\Generator as Faker;

$factory->define(Phone::class, function (Faker $faker) {
    return [
        'phone_number' => $faker->phoneNumber,
        'contact_id'   => factory(Contact::class),
    ];
});
