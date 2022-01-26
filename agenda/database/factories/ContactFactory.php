<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Phone;
use App\Models\Address;
use App\Models\Contact;
use Faker\Generator as Faker;

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'name'    => $faker->name,
        'user_id' => factory(User::class),
    ];
});

/*
|--------------------------------------------------------------------------
| Criação Entidades Relacionadas
|--------------------------------------------------------------------------
|
| Para possuir um contato completo, é necessário criar as entidadades que estão
| relacionadas ao contato, que são de forma onrigatória em sua criação. Após
| criar o contato, esses relacionamentos serão criados para completar o objeto
*/
$factory->afterMaking(Contact::class, function ($contact, $faker) {
    $contact->save();

    factory(Phone::class)->create([
        'contact_id' => $contact->id
    ]);

    factory(Address::class)->create([
        'contact_id' => $contact->id
    ]);

    $contact->save();
});
