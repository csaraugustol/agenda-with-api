<?php

use App\Models\User;
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
        /*factory(User::class, 5)->create()->each(function ($user) {
            factory(Contact::class, 10)->create(['user_id' => $user->id]);
        });*/
        factory(User::class, 5)->create()->each(function ($user) {
            factory(Contact::class, 2)->create(['user_id' => $user->id])->each(function ($contact) {
                factory(Address::class, 2)->create(['contact_id' => $contact->id]);
            });
        });
    }
}
