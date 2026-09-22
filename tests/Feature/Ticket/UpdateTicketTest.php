<?php

namespace Tests\Feature\Ticket;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class UpdateTicketTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_update_ticket(): void
    {
        $enterprise = $this->createEnterprise();
        $ticket = $this->createTicket([], $enterprise);

        $response = $this->patchJson("/api/tickets/{$ticket->id}", [
            'status' => 'in_progress',
        ]);

        $response->assertUnauthorized();
    }

    public function test_requester_can_update_their_own_ticket(): void
    {
        $enterprise = $this->createEnterprise();
        $requester = $this->createUser(['role' => 'requester'], $enterprise);
        $ticket = $this->createTicket([
            'requester_id' => $requester->id,
            'title' => 'Titulo Original',
            'description' => 'Descricao Original',
        ], $enterprise);

        Sanctum::actingAs($requester);

        $payload = [
            'title' => 'Titulo Atualizado pelo Solicitante',
            'description' => 'Descricao Atualizada pelo Solicitante',
        ];

        $response = $this->patchJson("/api/tickets/{$ticket->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Titulo Atualizado pelo Solicitante')
            ->assertJsonPath('data.description', 'Descricao Atualizada pelo Solicitante');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Titulo Atualizado pelo Solicitante',
        ]);
    }

    public function test_enterprise_admin_can_update_ticket_status_and_priority(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $ticket = $this->createTicket([
            'status' => 'open',
            'priority' => 'low',
        ], $enterprise);

        Sanctum::actingAs($admin);

        $payload = [
            'status' => 'in_progress',
            'priority' => 'high',
        ];

        $response = $this->patchJson("/api/tickets/{$ticket->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'in_progress')
            ->assertJsonPath('data.priority', 'high');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'in_progress',
            'priority' => 'high',
        ]);
    }

    public function test_enterprise_admin_can_assign_valid_attendant(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $attendant = $this->createUser(['role' => 'attendant'], $enterprise);
        $ticket = $this->createTicket([], $enterprise);

        Sanctum::actingAs($admin);

        $payload = [
            'attendant_id' => $attendant->id,
        ];

        $response = $this->patchJson("/api/tickets/{$ticket->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.attendant_id', $attendant->id);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'attendant_id' => $attendant->id,
        ]);
    }

    public function test_cannot_assign_requester_user_as_attendant(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $regularUser = $this->createUser(['role' => 'requester'], $enterprise);
        $ticket = $this->createTicket([], $enterprise);

        Sanctum::actingAs($admin);

        $payload = [
            'attendant_id' => $regularUser->id,
        ];

        $response = $this->patchJson("/api/tickets/{$ticket->id}", $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['attendant_id']);
    }

    public function test_cannot_assign_attendant_from_another_enterprise(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $adminA = $this->createUser(['role' => 'admin'], $enterpriseA);
        $attendantB = $this->createUser(['role' => 'attendant'], $enterpriseB);
        $ticketA = $this->createTicket([], $enterpriseA);

        Sanctum::actingAs($adminA);

        $payload = [
            'attendant_id' => $attendantB->id,
        ];

        $response = $this->patchJson("/api/tickets/{$ticketA->id}", $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['attendant_id']);
    }

    public function test_cannot_transfer_ticket_to_sector_of_another_enterprise(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $adminA = $this->createUser(['role' => 'admin'], $enterpriseA);
        $sectorB = $this->createSector([], $enterpriseB);
        $ticketA = $this->createTicket([], $enterpriseA);

        Sanctum::actingAs($adminA);

        $payload = [
            'sector_id' => $sectorB->id,
        ];

        $response = $this->patchJson("/api/tickets/{$ticketA->id}", $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sector_id']);
    }

    public function test_regular_user_cannot_update_ticket_created_by_another_user(): void
    {
        $enterprise = $this->createEnterprise();
        $user1 = $this->createUser(['role' => 'requester'], $enterprise);
        $user2 = $this->createUser(['role' => 'requester'], $enterprise);

        $ticket1 = $this->createTicket(['requester_id' => $user1->id], $enterprise);

        Sanctum::actingAs($user2);

        $response = $this->patchJson("/api/tickets/{$ticket1->id}", [
            'title' => 'Tentativa de alteracao indevida',
        ]);

        $response->assertForbidden();
    }
}
