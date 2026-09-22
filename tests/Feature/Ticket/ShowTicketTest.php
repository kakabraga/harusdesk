<?php

namespace Tests\Feature\Ticket;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class ShowTicketTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_view_ticket(): void
    {
        $enterprise = $this->createEnterprise();
        $ticket = $this->createTicket([], $enterprise);

        $response = $this->getJson("/api/tickets/{$ticket->id}");

        $response->assertUnauthorized();
    }

    public function test_requester_can_view_their_own_ticket(): void
    {
        $enterprise = $this->createEnterprise();
        $requester = $this->createUser(['role' => 'requester'], $enterprise);
        $ticket = $this->createTicket(['requester_id' => $requester->id], $enterprise);

        Sanctum::actingAs($requester);

        $response = $this->getJson("/api/tickets/{$ticket->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $ticket->id)
            ->assertJsonPath('data.requester_id', $requester->id);
    }

    public function test_enterprise_admin_can_view_any_ticket_from_their_enterprise(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $otherUser = $this->createUser(['role' => 'requester'], $enterprise);
        $ticket = $this->createTicket(['requester_id' => $otherUser->id], $enterprise);

        Sanctum::actingAs($admin);

        $response = $this->getJson("/api/tickets/{$ticket->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $ticket->id);
    }

    public function test_super_admin_can_view_ticket_from_any_enterprise(): void
    {
        $enterprise = $this->createEnterprise();
        $ticket = $this->createTicket([], $enterprise);
        $superAdmin = $this->createSuperAdmin();

        Sanctum::actingAs($superAdmin);

        $response = $this->getJson("/api/tickets/{$ticket->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $ticket->id);
    }

    public function test_user_cannot_view_ticket_from_another_enterprise(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $userA = $this->createUser(['role' => 'requester'], $enterpriseA);
        $ticketB = $this->createTicket([], $enterpriseB);

        Sanctum::actingAs($userA);

        $response = $this->getJson("/api/tickets/{$ticketB->id}");

        // Devido ao GlobalScope do Eloquent, o model nem é localizado no tenant (404) ou proibido pela policy (403)
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_returns_404_when_ticket_does_not_exist(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'admin'], $enterprise);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/tickets/99999');

        $response->assertNotFound();
    }
}
