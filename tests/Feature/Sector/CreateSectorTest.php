<?php

namespace Tests\Feature\Sector;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class CreateSectorTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithTestData;

    public function test_unauthenticated_user_cannot_create_sector(): void
    {
        $response = $this->postJson('/api/sectors', [
            'name' => 'Suporte',
        ]);

        $response->assertUnauthorized();
    }

    public function test_enterprise_admin_can_create_sector(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        Sanctum::actingAs($admin);

        $payload = [
            'name' => 'Recursos Humanos',
            'active' => true,
        ];

        $response = $this->postJson('/api/sectors', $payload);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Recursos Humanos')
            ->assertJsonPath('data.enterprise_id', $enterprise->id);

        $this->assertDatabaseHas('sectors', [
            'name' => 'Recursos Humanos',
            'enterprise_id' => $enterprise->id,
            'active' => true,
        ]);
    }

    public function test_super_admin_can_create_sector_by_providing_enterprise_id(): void
    {
        $enterprise = $this->createEnterprise();
        $superAdmin = $this->createSuperAdmin();

        Sanctum::actingAs($superAdmin);

        $payload = [
            'name' => 'Setor via SuperAdmin',
            'enterprise_id' => $enterprise->id,
            'active' => true,
        ];

        $response = $this->postJson('/api/sectors', $payload);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Setor via SuperAdmin')
            ->assertJsonPath('data.enterprise_id', $enterprise->id);

        $this->assertDatabaseHas('sectors', [
            'name' => 'Setor via SuperAdmin',
            'enterprise_id' => $enterprise->id,
        ]);
    }

    public function test_regular_users_cannot_create_sector(): void
    {
        $enterprise = $this->createEnterprise();

        foreach (['attendant', 'requester'] as $role) {
            $user = $this->createUser(['role' => $role], $enterprise);
            Sanctum::actingAs($user);

            $response = $this->postJson('/api/sectors', [
                'name' => 'Setor Não Permitido',
            ]);

            $response->assertForbidden();
        }
    }

    public function test_enterprise_admin_cannot_provide_enterprise_id_in_payload(): void
    {
        $enterprise = $this->createEnterprise();
        $otherEnterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        Sanctum::actingAs($admin);

        $payload = [
            'name' => 'Setor Injetado',
            'enterprise_id' => $otherEnterprise->id,
        ];

        $response = $this->postJson('/api/sectors', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['enterprise_id']);
    }

    public function test_super_admin_must_provide_enterprise_id(): void
    {
        $superAdmin = $this->createSuperAdmin();

        Sanctum::actingAs($superAdmin);

        $payload = [
            'name' => 'Setor Sem Enterprise',
        ];

        $response = $this->postJson('/api/sectors', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['enterprise_id']);
    }

    public function test_super_admin_cannot_provide_non_existing_enterprise_id(): void
    {
        $superAdmin = $this->createSuperAdmin();

        Sanctum::actingAs($superAdmin);

        $payload = [
            'name' => 'Setor Enterprise Inexistente',
            'enterprise_id' => 99999,
        ];

        $response = $this->postJson('/api/sectors', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['enterprise_id']);
    }

    public function test_creation_fails_when_name_is_missing(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/sectors', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_creation_fails_when_name_exceeds_max_length(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/sectors', [
            'name' => str_repeat('a', 256),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_creation_fails_when_active_is_not_boolean(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/sectors', [
            'name' => 'Setor Teste',
            'active' => 'invalid-boolean-value',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['active']);
    }
}
