<?php

namespace Tests\Feature\Sector;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class UpdateSectorTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_update_sector(): void
    {
        $sector = $this->createSector();

        $response = $this->putJson("/api/sectors/{$sector->id}", [
            'name' => 'Novo Nome',
        ]);

        $response->assertUnauthorized();
    }

    public function test_enterprise_admin_can_fully_update_sector(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $sector = $this->createSector([
            'name' => 'Nome Antigo',
            'active' => true,
            'accepts_tickets' => false,
        ], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/sectors/{$sector->id}", [
            'name' => 'Nome Atualizado',
            'active' => false,
            'accepts_tickets' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Nome Atualizado')
            ->assertJsonPath('data.active', false)
            ->assertJsonPath('data.accepts_tickets', true);

        $this->assertDatabaseHas('sectors', [
            'id' => $sector->id,
            'name' => 'Nome Atualizado',
            'active' => false,
            'accepts_tickets' => true,
        ]);
    }

    public function test_enterprise_admin_can_partially_update_only_name_without_changing_active(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $sector = $this->createSector([
            'name' => 'Financeiro Antigo',
            'active' => true,
        ], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/sectors/{$sector->id}", [
            'name' => 'Financeiro Novo',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Financeiro Novo')
            ->assertJsonPath('data.active', true);

        $this->assertDatabaseHas('sectors', [
            'id' => $sector->id,
            'name' => 'Financeiro Novo',
            'active' => true,
        ]);
    }

    public function test_enterprise_admin_can_partially_update_only_active_without_changing_name(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $sector = $this->createSector([
            'name' => 'Suporte N1',
            'active' => true,
        ], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/sectors/{$sector->id}", [
            'active' => false,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Suporte N1')
            ->assertJsonPath('data.active', false);

        $this->assertDatabaseHas('sectors', [
            'id' => $sector->id,
            'name' => 'Suporte N1',
            'active' => false,
        ]);
    }

    public function test_super_admin_can_update_sector_of_any_enterprise(): void
    {
        $enterprise = $this->createEnterprise();
        $superAdmin = $this->createSuperAdmin();
        $sector = $this->createSector([
            'name' => 'Setor Empresa X',
            'active' => true,
        ], $enterprise);

        Sanctum::actingAs($superAdmin);

        $response = $this->putJson("/api/sectors/{$sector->id}", [
            'name' => 'Setor Atualizado por SuperAdmin',
            'active' => false,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Setor Atualizado por SuperAdmin')
            ->assertJsonPath('data.active', false);
    }

    public function test_enterprise_admin_cannot_update_sector_from_another_enterprise(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $adminA = $this->createUser(['role' => 'admin'], $enterpriseA);
        $sectorB = $this->createSector(['name' => 'Setor B'], $enterpriseB);

        Sanctum::actingAs($adminA);

        $response = $this->putJson("/api/sectors/{$sectorB->id}", [
            'name' => 'Tentativa de Hack',
        ]);

        $this->assertTrue(in_array($response->status(), [403, 404]));

        $this->assertDatabaseHas('sectors', [
            'id' => $sectorB->id,
            'name' => 'Setor B',
        ]);
    }

    public function test_regular_users_cannot_update_sector(): void
    {
        $enterprise = $this->createEnterprise();
        $sector = $this->createSector(['name' => 'Setor Original'], $enterprise);

        foreach (['attendant', 'requester'] as $role) {
            $user = $this->createUser(['role' => $role], $enterprise);
            Sanctum::actingAs($user);

            $response = $this->putJson("/api/sectors/{$sector->id}", [
                'name' => 'Tentativa de Alteração',
            ]);

            $response->assertForbidden();
        }
    }

    public function test_update_fails_when_name_exceeds_max_length(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $sector = $this->createSector([], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/sectors/{$sector->id}", [
            'name' => str_repeat('b', 256),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_enterprise_admin_can_partially_update_only_accepts_tickets_without_changing_other_fields(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $sector = $this->createSector([
            'name' => 'Atendimento Geral',
            'active' => true,
            'accepts_tickets' => false,
        ], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/sectors/{$sector->id}", [
            'accepts_tickets' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Atendimento Geral')
            ->assertJsonPath('data.active', true)
            ->assertJsonPath('data.accepts_tickets', true);

        $this->assertDatabaseHas('sectors', [
            'id' => $sector->id,
            'name' => 'Atendimento Geral',
            'active' => true,
            'accepts_tickets' => true,
        ]);
    }

    public function test_update_fails_when_active_is_not_boolean(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $sector = $this->createSector([], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/sectors/{$sector->id}", [
            'active' => 'not-a-bool',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['active']);
    }

    public function test_update_fails_when_accepts_tickets_is_not_boolean(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $sector = $this->createSector([], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/sectors/{$sector->id}", [
            'accepts_tickets' => 'not-a-bool',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['accepts_tickets']);
    }
}
