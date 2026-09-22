<?php

namespace Tests\Feature\Ticket;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class ListTicketTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_list_tickets(): void
    {
        $response = $this->getJson('/api/tickets');

        $response->assertUnauthorized();
    }

    public function test_user_can_list_only_tickets_from_their_own_enterprise(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $adminA = $this->createUser(['role' => 'admin'], $enterpriseA);
        $sectorA = $this->createSector([], $enterpriseA);
        $this->createTicket(['title' => 'Ticket Empresa A 1', 'sector_id' => $sectorA->id], $enterpriseA);
        $this->createTicket(['title' => 'Ticket Empresa A 2', 'sector_id' => $sectorA->id], $enterpriseA);

        $sectorB = $this->createSector([], $enterpriseB);
        $this->createTicket(['title' => 'Ticket Empresa B', 'sector_id' => $sectorB->id], $enterpriseB);

        Sanctum::actingAs($adminA);

        $response = $this->getJson('/api/tickets');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');

        $titles = collect($response->json('data'))->pluck('title')->toArray();
        $this->assertContains('Ticket Empresa A 1', $titles);
        $this->assertContains('Ticket Empresa A 2', $titles);
        $this->assertNotContains('Ticket Empresa B', $titles);
    }

    public function test_super_admin_can_list_tickets_from_all_enterprises(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $this->createTicket(['title' => 'Ticket Enterprise A'], $enterpriseA);
        $this->createTicket(['title' => 'Ticket Enterprise B'], $enterpriseB);

        $superAdmin = $this->createSuperAdmin();
        Sanctum::actingAs($superAdmin);

        $response = $this->getJson('/api/tickets');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');
    }

    public function test_super_admin_can_filter_tickets_by_enterprise_id(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $this->createTicket(['title' => 'Ticket Enterprise A'], $enterpriseA);
        $this->createTicket(['title' => 'Ticket Enterprise B'], $enterpriseB);

        $superAdmin = $this->createSuperAdmin();
        Sanctum::actingAs($superAdmin);

        $response = $this->getJson("/api/tickets?enterprise_id={$enterpriseA->id}");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Ticket Enterprise A');
    }

    public function test_regular_user_cannot_filter_by_enterprise_id(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterpriseA);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/tickets?enterprise_id={$enterpriseB->id}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['enterprise_id']);
    }

    public function test_user_can_filter_tickets_by_status(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        $this->createTicket(['status' => 'open', 'title' => 'Ticket Aberto'], $enterprise);
        $this->createTicket(['status' => 'in_progress', 'title' => 'Ticket Em Andamento'], $enterprise);
        $this->createTicket(['status' => 'concluded', 'title' => 'Ticket Concluido'], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/tickets?status=in_progress');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Ticket Em Andamento');
    }

    public function test_user_can_filter_tickets_by_priority(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        $this->createTicket(['priority' => 'low', 'title' => 'Prioridade Baixa'], $enterprise);
        $this->createTicket(['priority' => 'high', 'title' => 'Prioridade Alta'], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/tickets?priority=high');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Prioridade Alta');
    }

    public function test_user_can_filter_tickets_by_sector(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        $sectorTI = $this->createSector(['name' => 'TI'], $enterprise);
        $sectorRH = $this->createSector(['name' => 'RH'], $enterprise);

        $this->createTicket(['sector_id' => $sectorTI->id, 'title' => 'Chamado TI'], $enterprise);
        $this->createTicket(['sector_id' => $sectorRH->id, 'title' => 'Chamado RH'], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->getJson("/api/tickets?sector_id={$sectorTI->id}");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Chamado TI');
    }
}
