<?php

namespace Tests\Feature;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function registerRoleAndPermission()
    {
        if (Role::where('name', config('permission.default_roles'))->count() < 1) {
            foreach (config('permission.default_roles') as $role) {
                Role::create(['name' => $role]);
            }
        }
        if (Permission::where('name', config('permission.default_permissions'))->count() < 1) {
            foreach (config('permission.default_permissions') as $permission) {
                Permission::create(['name' => $permission]);
            }
        }

    }

    /**
     * Register Tests
     */
    public function test_register_should_be_validated()
    {
        $response = $this->postJson(route('auth.register'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_new_user_can_register()
    {
        $this->registerRoleAndPermission();
        $response = $this->postJson(route('auth.register'), [
            'name' => 'sanaz keyvanloo',
            'email' => 'sanaz.keyvanloo1992@gmail.com',
            'password' => '12345678'
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Login Tests
     */
    public function test_login_should_be_validate()
    {
        $response = $this->postJson(route('auth.login'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_user_can_login_with_true_credentials()
    {
        $user = User::factory()->create();
        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password'
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Logged Out User Tests
     */
    public function test_logged_in_user_can_logout()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson(route('auth.logout'));
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Logged In User
     */
    public function test_show_user_info_if_logged_in()
    {
        $user = User::factory()->create();
        $response = $this
            ->actingAs($user)
            ->get(route('auth.user'));
        $response->assertStatus(Response::HTTP_OK);
    }
}
