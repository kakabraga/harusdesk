<?php

namespace Tests\Feature\Sector;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class DeleteSectorTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithTestData;

    public function test_unauthenticated_user_cannot_delete_sector(): void
    {
        $sector = $this->createSector();

        $response = $this->deleteJson("/api/sectors/{$sector->id}");

        $response->assertUnauthorized();
    }

    public function test_enterprise_admin_can_delete_sector_from_their_own_enterprise(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $sector = $this->createSector(['name' => 'Setor a Deletar'], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/sectors/{$sector->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('sectors', [
            'id' => $sector->id,
        ]);
    }

    public function test_super_admin_can_delete_sector_from_any_enterprise(): void
    {
        $enterprise = $this->createEnterprise();
        $superAdmin = $this->createSuperAdmin();
        $sector = $this->createSector(['name' => 'Setor Empresa Global'], $enterprise);

        Sanctum::actingAs($superAdmin);

        $response = $this->deleteJson("/api/sectors/{$sector->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('sectors', [
            'id' => $sector->id,
        ]);
    }

    public function test_enterprise_admin_cannot_delete_sector_from_another_enterprise(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $adminA = $this->createUser(['role' => 'admin'], $enterpriseA);
        $sectorB = $this->createSector(['name' => 'Setor B Intocável'], $enterpriseB);

        Sanctum::actingAs($adminA);

        $response = $this->deleteJson("/api/sectors/{$sectorB->id}");

        $this->assertTrue(in_array($response->status(), [403, 404]));

        $this->assertDatabaseHas('sectors', [
            'id' => $sectorB->id,
            'name' => 'Setor B Intocável',
        ]);
    }

    public function test_regular_users_cannot_delete_sector(): void
    {
        $enterprise = $this->createEnterprise();
        $sector = $this->createSector(['name' => 'Setor Protegido'], $enterprise);

        foreach (['attendant', 'requester'] as $role) {
            $user = $this->createUser(['role' => $role], $enterprise);
            Sanctum::actingAs($user);

            $response = $this->deleteJson("/api/sectors/{$sector->id}");

            $response->assertForbidden();

            $this->assertDatabaseHas('sectors', [
                'id' => $sector->id,
            ]);
        }
    }

    public function test_returns_404_when_deleting_non_existent_sector(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->deleteJson('/api/sectors/99999');

        $response->assertNotFound();
    }
}
