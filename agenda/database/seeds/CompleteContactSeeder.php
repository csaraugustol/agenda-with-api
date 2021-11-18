<?php

use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class CompleteContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 5)->create()->each(function ($user) {
            $user->contacts()
            ->save(factory(Contact::class)
            ->create(['user_id' => $user->id]));
        });
    }
}
