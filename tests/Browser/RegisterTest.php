<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Chrome;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Repositories\User\User;

class RegisterTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test the registration form.
     *
     * @return void
     */
    public function testRegister()
    {
        $user = factory(User::class)->make();

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/register')
                    ->waitFor('#name')
                    ->type('name', $user->name)
                    ->type('email', $user->email)
                    ->type('password', 'secret')
                    ->type('password_confirmation', 'secret')
                    ->press('Register')
                    ->assertPathIs('/dashboard/admin')
                    ->assertSee($user->name);
        });
    }
}
