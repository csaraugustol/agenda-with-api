<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Tag;
use App\Models\Contact;
use App\Models\TagContact;
use Faker\Generator as Faker;

$factory->define(TagContact::class, function (Faker $faker) {
    return [
        'tag_id'     => factory(Tag::class),
        'contact_id' => factory(Contact::class),
    ];
});
