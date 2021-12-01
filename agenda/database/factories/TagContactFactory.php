<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Tag;
use App\Models\Contact;
use App\Models\TagContact;

$factory->define(TagContact::class, function () {
    return [
        'tag_id'     => factory(Tag::class),
        'contact_id' => factory(Contact::class),
    ];
});
