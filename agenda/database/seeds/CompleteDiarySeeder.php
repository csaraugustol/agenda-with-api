<?php

use App\Models\Tag;
use App\Models\User;
use App\Models\Phone;
use App\Models\Address;
use App\Models\Contact;
use App\Models\TagContact;
use App\Models\ChangePassword;
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
        factory(User::class, 3)->create()->each(function ($user) {
            factory(ChangePassword::class)->create(['user_id' => $user->id]);
            $tags = factory(Tag::class, 2)->create(['user_id' => $user->id]);
            factory(Contact::class, 2)->create(['user_id' => $user->id])->each(function ($contact) use ($tags) {
                foreach ($tags as $tag) {
                    factory(TagContact::class)->create([
                        'tag_id' => $tag->id,
                        'contact_id' => $contact->id
                    ]);
                }

                factory(Address::class, 2)->create(['contact_id' => $contact->id]);
                factory(Phone::class, 3)->create(['contact_id' => $contact->id]);
            });
        });
    }
}
