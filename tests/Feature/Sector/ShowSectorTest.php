<?php

namespace Tests\Feature\Sector;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class ShowSectorTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_view_sector(): void
    {
        $sector = $this->createSector();

        $response = $this->getJson("/api/sectors/{$sector->id}");

        $response->assertUnauthorized();
    }

    public function test_user_can_view_sector_from_their_own_enterprise(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'admin'], $enterprise);
        $sector = $this->createSector(['name' => 'Desenvolvimento'], $enterprise);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/sectors/{$sector->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $sector->id)
            ->assertJsonPath('data.name', 'Desenvolvimento')
            ->assertJsonPath('data.enterprise_id', $enterprise->id)
            ->assertJsonPath('data.active', true)
            ->assertJsonPath('data.accepts_tickets', true);
    }

    public function test_user_cannot_view_sector_from_another_enterprise(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $userA = $this->createUser(['role' => 'admin'], $enterpriseA);
        $sectorB = $this->createSector(['name' => 'Setor Empresa B'], $enterpriseB);

        Sanctum::actingAs($userA);

        $response = $this->getJson("/api/sectors/{$sectorB->id}");

        // Due to Global Scope or Policy, user A cannot access sector B
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_super_admin_can_view_sector_from_any_enterprise(): void
    {
        $enterprise = $this->createEnterprise();
        $superAdmin = $this->createSuperAdmin();
        $sector = $this->createSector(['name' => 'Setor Global'], $enterprise);

        Sanctum::actingAs($superAdmin);

        $response = $this->getJson("/api/sectors/{$sector->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $sector->id)
            ->assertJsonPath('data.name', 'Setor Global');
    }

    public function test_returns_404_when_sector_does_not_exist(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'admin'], $enterprise);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/sectors/99999');

        $response->assertNotFound();
    }
}
