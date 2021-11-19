<?php

use App\Models\Tag;
use App\Models\User;
use App\Models\Phone;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class CompleteDiarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 5)->create()->each(function ($user) {
            factory(Tag::class, 3)->create(['user_id' => $user->id]);
            factory(Contact::class, 2)->create(['user_id' => $user->id])->each(function ($contact) {
                factory(Address::class, 2)->create(['contact_id' => $contact->id]);
                factory(Phone::class, 3)->create(['contact_id' => $contact->id]);
            });
        });
    }
}
