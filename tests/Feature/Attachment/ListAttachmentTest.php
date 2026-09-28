<?php

namespace Tests\Feature\Attachment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class ListAttachmentTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_list_attachments(): void
    {
        $response = $this->getJson('/api/attachments');

        $response->assertUnauthorized();
    }

    public function test_regular_user_cannot_list_attachments(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterprise);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/attachments');

        $response->assertForbidden();
    }

    public function test_enterprise_admin_can_list_attachments_from_their_enterprise(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $adminA = $this->createUser(['role' => 'admin'], $enterpriseA);

        $ticketA = $this->createTicket([], $enterpriseA);
        $ticketB = $this->createTicket([], $enterpriseB);

        $attachmentA1 = $this->createAttachment(['original_name' => 'docA1.pdf'], $ticketA);
        $attachmentA2 = $this->createAttachment(['original_name' => 'docA2.pdf'], $ticketA);
        $this->createAttachment(['original_name' => 'docB.pdf'], $ticketB);

        Sanctum::actingAs($adminA);

        $response = $this->getJson('/api/attachments');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');

        $names = collect($response->json('data'))->pluck('original_name')->all();
        $this->assertContains('docA1.pdf', $names);
        $this->assertContains('docA2.pdf', $names);
    }

    public function test_super_admin_can_list_attachments_from_all_enterprises(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $superAdmin = $this->createSuperAdmin();

        $ticketA = $this->createTicket([], $enterpriseA);
        $ticketB = $this->createTicket([], $enterpriseB);

        $this->createAttachment(['original_name' => 'docA.pdf'], $ticketA);
        $this->createAttachment(['original_name' => 'docB.pdf'], $ticketB);

        Sanctum::actingAs($superAdmin);

        $response = $this->getJson('/api/attachments');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');
    }

    public function test_super_admin_can_filter_attachments_by_enterprise_id(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $superAdmin = $this->createSuperAdmin();

        $ticketA = $this->createTicket([], $enterpriseA);
        $ticketB = $this->createTicket([], $enterpriseB);

        $this->createAttachment(['original_name' => 'docA.pdf'], $ticketA);
        $this->createAttachment(['original_name' => 'docB.pdf'], $ticketB);

        Sanctum::actingAs($superAdmin);

        $response = $this->getJson("/api/attachments?enterprise_id={$enterpriseA->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.original_name', 'docA.pdf');
    }

    public function test_enterprise_admin_cannot_filter_by_enterprise_id(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $adminA = $this->createUser(['role' => 'admin'], $enterpriseA);

        Sanctum::actingAs($adminA);

        $response = $this->getJson("/api/attachments?enterprise_id={$enterpriseB->id}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['enterprise_id']);
    }

    public function test_can_filter_attachments_by_ticket_id(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        $ticket1 = $this->createTicket([], $enterprise);
        $ticket2 = $this->createTicket([], $enterprise);

        $this->createAttachment(['original_name' => 'ticket1_file.pdf'], $ticket1);
        $this->createAttachment(['original_name' => 'ticket2_file.pdf'], $ticket2);

        Sanctum::actingAs($admin);

        $response = $this->getJson("/api/attachments?ticket_id={$ticket1->id}");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.original_name', 'ticket1_file.pdf');
    }

    public function test_can_filter_attachments_by_original_name_and_mime_type(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        $ticket = $this->createTicket([], $enterprise);

        $this->createAttachment([
            'original_name' => 'relatorio_financeiro.pdf',
            'mime_type' => 'application/pdf',
        ], $ticket);

        $this->createAttachment([
            'original_name' => 'imagem_foto.png',
            'mime_type' => 'image/png',
        ], $ticket);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/attachments?original_name=relatorio&mime_type=application/pdf');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.original_name', 'relatorio_financeiro.pdf');
    }

    public function test_can_filter_attachments_by_size_range(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);

        $ticket = $this->createTicket([], $enterprise);

        $this->createAttachment(['original_name' => 'pequeno.pdf', 'size_kb' => 50], $ticket);
        $this->createAttachment(['original_name' => 'medio.pdf', 'size_kb' => 300], $ticket);
        $this->createAttachment(['original_name' => 'grande.pdf', 'size_kb' => 800], $ticket);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/attachments?min_size_kb=100&max_size_kb=500');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.original_name', 'medio.pdf');
    }
}
