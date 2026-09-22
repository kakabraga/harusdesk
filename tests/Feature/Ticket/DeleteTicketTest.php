<?php

namespace Tests\Feature\Ticket;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class DeleteTicketTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_delete_ticket(): void
    {
        $enterprise = $this->createEnterprise();
        $ticket = $this->createTicket([], $enterprise);

        $response = $this->deleteJson("/api/tickets/{$ticket->id}");

        $response->assertUnauthorized();
    }

    public function test_enterprise_admin_can_soft_delete_ticket_from_their_own_enterprise(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $ticket = $this->createTicket([], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/tickets/{$ticket->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('tickets', [
            'id' => $ticket->id,
        ]);
    }

    public function test_super_admin_can_delete_ticket_from_any_enterprise(): void
    {
        $enterprise = $this->createEnterprise();
        $ticket = $this->createTicket([], $enterprise);
        $superAdmin = $this->createSuperAdmin();

        Sanctum::actingAs($superAdmin);

        $response = $this->deleteJson("/api/tickets/{$ticket->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('tickets', [
            'id' => $ticket->id,
        ]);
    }

    public function test_enterprise_admin_cannot_delete_ticket_from_another_enterprise(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $adminA = $this->createUser(['role' => 'admin'], $enterpriseA);
        $ticketB = $this->createTicket([], $enterpriseB);

        Sanctum::actingAs($adminA);

        $response = $this->deleteJson("/api/tickets/{$ticketB->id}");

        $this->assertTrue(in_array($response->status(), [403, 404]));

        $this->assertDatabaseHas('tickets', [
            'id' => $ticketB->id,
            'deleted_at' => null,
        ]);
    }

    public function test_regular_user_cannot_delete_ticket(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterprise);
        $ticket = $this->createTicket(['requester_id' => $user->id], $enterprise);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/tickets/{$ticket->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'deleted_at' => null,
        ]);
    }

    public function test_returns_404_when_deleting_non_existent_ticket(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->deleteJson('/api/tickets/99999');

        $response->assertNotFound();
    }
}
