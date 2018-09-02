<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Repositories\User\User;

class RegisterTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test if the register page is hidden if users already exist.
     *
     * @return void
     */
    public function testRegisterPageIsHiddenWhenUsersExist()
    {
        factory(User::class)->create();

        $response = $this->get('/register');

        $response->assertStatus(404);
    }
}
