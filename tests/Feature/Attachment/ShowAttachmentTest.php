<?php

namespace Tests\Feature\Attachment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class ShowAttachmentTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_view_attachment(): void
    {
        $attachment = $this->createAttachment();

        $response = $this->getJson("/api/attachments/{$attachment->id}");

        $response->assertUnauthorized();
    }

    public function test_requester_can_view_their_ticket_attachment(): void
    {
        $enterprise = $this->createEnterprise();
        $requester = $this->createUser(['role' => 'requester'], $enterprise);
        $ticket = $this->createTicket(['requester_id' => $requester->id], $enterprise);
        $attachment = $this->createAttachment(['original_name' => 'meu_anexo.pdf'], $ticket);

        Sanctum::actingAs($requester);

        $response = $this->getJson("/api/attachments/{$attachment->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $attachment->id)
            ->assertJsonPath('data.original_name', 'meu_anexo.pdf')
            ->assertJsonPath('data.ticket_id', $ticket->id);
    }

    public function test_enterprise_admin_can_view_attachment_from_their_enterprise(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $ticket = $this->createTicket([], $enterprise);
        $attachment = $this->createAttachment([], $ticket);

        Sanctum::actingAs($admin);

        $response = $this->getJson("/api/attachments/{$attachment->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $attachment->id);
    }

    public function test_sector_user_can_view_attachment_from_their_sector_ticket(): void
    {
        $enterprise = $this->createEnterprise();
        $sector = $this->createSector([], $enterprise);
        $attendant = $this->createUser(['role' => 'attendant'], $enterprise);
        $attendant->sectors()->attach($sector->id);

        $requester = $this->createUser(['role' => 'requester'], $enterprise);
        $ticket = $this->createTicket([
            'sector_id' => $sector->id,
            'requester_id' => $requester->id,
        ], $enterprise);

        $attachment = $this->createAttachment([], $ticket);

        Sanctum::actingAs($attendant);

        $response = $this->getJson("/api/attachments/{$attachment->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $attachment->id);
    }

    public function test_user_cannot_view_attachment_from_another_enterprise(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $userA = $this->createUser(['role' => 'requester'], $enterpriseA);
        $ticketB = $this->createTicket([], $enterpriseB);
        $attachmentB = $this->createAttachment([], $ticketB);

        Sanctum::actingAs($userA);

        $response = $this->getJson("/api/attachments/{$attachmentB->id}");

        $response->assertForbidden();
    }

    public function test_user_cannot_view_attachment_from_another_user_in_different_sector(): void
    {
        $enterprise = $this->createEnterprise();
        $sector1 = $this->createSector([], $enterprise);
        $sector2 = $this->createSector([], $enterprise);

        $requester1 = $this->createUser(['role' => 'requester'], $enterprise);
        $requester2 = $this->createUser(['role' => 'requester'], $enterprise);
        $requester2->sectors()->attach($sector2->id);

        $ticket1 = $this->createTicket([
            'sector_id' => $sector1->id,
            'requester_id' => $requester1->id,
        ], $enterprise);

        $attachment1 = $this->createAttachment([], $ticket1);

        Sanctum::actingAs($requester2);

        $response = $this->getJson("/api/attachments/{$attachment1->id}");

        $response->assertForbidden();
    }

    public function test_super_admin_can_view_attachment_from_any_enterprise(): void
    {
        $enterprise = $this->createEnterprise();
        $superAdmin = $this->createSuperAdmin();
        $ticket = $this->createTicket([], $enterprise);
        $attachment = $this->createAttachment([], $ticket);

        Sanctum::actingAs($superAdmin);

        $response = $this->getJson("/api/attachments/{$attachment->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $attachment->id);
    }

    public function test_returns_404_when_attachment_does_not_exist(): void
    {
        $superAdmin = $this->createSuperAdmin();

        Sanctum::actingAs($superAdmin);

        $response = $this->getJson('/api/attachments/999999');

        $response->assertNotFound();
    }
}
