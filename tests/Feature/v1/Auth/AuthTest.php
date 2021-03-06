<?php

namespace Tests\Feature\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;


    /*
    * test for register
    */

    public function test_register_request_should_be_validate()
    {
        $response = $this->postJson(route('auth.register'));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    public function test_register_user()
    {
        $data = [
            'name' => $this->faker()->name(),
            'email' => $this->faker()->email(),
            'password' => bcrypt('password')
        ];
        $response = $this->postJson(route('auth.register'), $data);
        $response->assertStatus(Response::HTTP_CREATED);
    }




    /*
     * test for login
     */

    public function test_login_request_should_be_validate()
    {
        $response = $this->postJson(route('auth.login'));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    public function test_login_user()
    {
        $user = User::factory()->create();
        $data = ['email' => $user->email, 'password' => 'password'];
        $response = $this->postJson(route('auth.login'), $data);
        $response->assertStatus(Response::HTTP_OK);
    }


    /*
     * test for logout
     */

    public function test_logout_request_should_be_validate()
    {
        $response = $this->postJson(route('auth.logout'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
    public function test_logout_user()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        $response = $this->postJson(route('auth.logout'));
        $response->assertStatus(Response::HTTP_OK);
    }
}
