<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contact = factory(Contact::class, 7)->create();
        $user = factory(User::class)->make();
        $contact->user()->create($user);
    }
}
