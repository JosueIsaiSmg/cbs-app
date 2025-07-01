<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_register_a_new_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Usuario registrado exitosamente'
                ])
                ->assertJsonStructure([
                    'message',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at'
                    ],
                    'token'
                ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email']
        ]);

        // Verificar que la contraseña está hasheada
        $user = User::where('email', $userData['email'])->first();
        $this->assertTrue(Hash::check($userData['password'], $user->password));
    }

    /** @test */
    public function it_can_login_an_existing_user()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Login exitoso'
                ])
                ->assertJsonStructure([
                    'message',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at'
                    ],
                    'token'
                ]);
    }

    /** @test */
    public function it_can_logout_a_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Logout exitoso'
                ]);
    }

    /** @test */
    public function it_validates_required_fields_on_register()
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'name',
                    'email',
                    'password'
                ]);
    }

    /** @test */
    public function it_validates_email_format_on_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_validates_password_confirmation_on_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function it_validates_unique_email_on_register()
    {
        $existingUser = User::factory()->create();

        $userData = [
            'name' => 'Test User',
            'email' => $existingUser->email,
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_validates_required_fields_on_login()
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'email',
                    'password'
                ]);
    }

    /** @test */
    public function it_validates_email_format_on_login()
    {
        $loginData = [
            'email' => 'invalid-email',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_rejects_invalid_credentials_on_login()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => 'wrong-password'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Credenciales inválidas'
                ]);
    }

    /** @test */
    public function it_rejects_nonexistent_user_on_login()
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Credenciales inválidas'
                ]);
    }

    /** @test */
    public function it_requires_authentication_for_logout()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_user_profile_when_authenticated()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/profile');

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ]);
    }

    /** @test */
    public function it_requires_authentication_for_profile()
    {
        $response = $this->getJson('/api/auth/profile');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_update_user_profile()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ];

        $response = $this->putJson('/api/auth/profile', $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Perfil actualizado exitosamente',
                    'data' => [
                        'id' => $user->id,
                        'name' => $updateData['name'],
                        'email' => $updateData['email']
                    ]
                ]);

        $this->assertDatabaseHas('users', $updateData);
    }

    /** @test */
    public function it_validates_profile_update_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $updateData = [
            'name' => '',
            'email' => 'invalid-email'
        ];

        $response = $this->putJson('/api/auth/profile', $updateData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email']);
    }

    /** @test */
    public function it_validates_unique_email_on_profile_update()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);

        $updateData = [
            'name' => 'Updated Name',
            'email' => $user2->email // Email de otro usuario
        ];

        $response = $this->putJson('/api/auth/profile', $updateData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_can_change_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password')
        ]);
        Sanctum::actingAs($user);

        $passwordData = [
            'current_password' => 'old-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123'
        ];

        $response = $this->putJson('/api/auth/change-password', $passwordData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Contraseña actualizada exitosamente'
                ]);

        // Verificar que la nueva contraseña funciona
        $user->refresh();
        $this->assertTrue(Hash::check('new-password123', $user->password));
    }

    /** @test */
    public function it_validates_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password')
        ]);
        Sanctum::actingAs($user);

        $passwordData = [
            'current_password' => 'wrong-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123'
        ];

        $response = $this->putJson('/api/auth/change-password', $passwordData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['current_password']);
    }

    /** @test */
    public function it_validates_password_confirmation_on_change()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password')
        ]);
        Sanctum::actingAs($user);

        $passwordData = [
            'current_password' => 'old-password',
            'password' => 'new-password123',
            'password_confirmation' => 'different-password'
        ];

        $response = $this->putJson('/api/auth/change-password', $passwordData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }
} 