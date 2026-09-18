<?php

namespace Tests\Feature\Sector;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class ListSectorTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithTestData;

    public function test_unauthenticated_user_cannot_list_sectors(): void
    {
        $response = $this->getJson('/api/sectors');

        $response->assertUnauthorized();
    }

    public function test_user_can_list_only_sectors_from_their_own_enterprise(): void
    {
        $enterpriseA = $this->createEnterprise(['name' => 'Empresa A']);
        $enterpriseB = $this->createEnterprise(['name' => 'Empresa B']);

        $userA = $this->createUser(['role' => 'admin'], $enterpriseA);

        $sectorA1 = $this->createSector(['name' => 'TI Empresa A'], $enterpriseA);
        $sectorA2 = $this->createSector(['name' => 'RH Empresa A'], $enterpriseA);
        $sectorB = $this->createSector(['name' => 'Financeiro Empresa B'], $enterpriseB);

        Sanctum::actingAs($userA);

        $response = $this->getJson('/api/sectors');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => $sectorA1->name])
            ->assertJsonFragment(['name' => $sectorA2->name])
            ->assertJsonMissing(['name' => $sectorB->name]);
    }

    public function test_super_admin_can_list_sectors_from_all_enterprises(): void
    {
        $enterpriseA = $this->createEnterprise(['name' => 'Empresa A']);
        $enterpriseB = $this->createEnterprise(['name' => 'Empresa B']);

        $superAdmin = $this->createSuperAdmin();

        $sectorA = $this->createSector(['name' => 'TI Empresa A'], $enterpriseA);
        $sectorB = $this->createSector(['name' => 'Financeiro Empresa B'], $enterpriseB);

        Sanctum::actingAs($superAdmin);

        $response = $this->getJson('/api/sectors');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => $sectorA->name])
            ->assertJsonFragment(['name' => $sectorB->name]);
    }

    public function test_returns_empty_list_when_enterprise_has_no_sectors(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'admin'], $enterprise);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/sectors');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', []);
    }
}
