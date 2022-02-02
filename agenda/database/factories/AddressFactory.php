<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Address;
use App\Models\Contact;
use Faker\Generator as Faker;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'street_name'  => $faker->streetName,
        'number'       => $faker->buildingNumber,
        'complement'   => $faker->secondaryAddress,
        'neighborhood' => $faker->streetSuffix,
        'city'         => $faker->city,
        'state'        => $faker->state,
        'postal_code'  => $faker->regexify('[0-9]{8}'),
        'country'      => $faker->country,
        'contact_id'   => factory(Contact::class),
    ];
});
