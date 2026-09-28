<?php

namespace Tests\Feature\Attachment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class DeleteAttachmentTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_delete_attachment(): void
    {
        $attachment = $this->createAttachment();

        $response = $this->deleteJson("/api/attachments/{$attachment->id}");

        $response->assertUnauthorized();
    }

    public function test_requester_can_delete_their_ticket_attachment_moving_to_trash(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $requester = $this->createUser(['role' => 'requester'], $enterprise);
        $ticket = $this->createTicket(['requester_id' => $requester->id, 'has_attachments' => true], $enterprise);

        $originalPath = "attachments/enterprise_{$enterprise->id}/ticket_{$ticket->id}/arquivo.pdf";
        Storage::disk('local')->put($originalPath, 'arquivo original');

        $attachment = $this->createAttachment([
            'path' => $originalPath,
        ], $ticket);

        Sanctum::actingAs($requester);

        $response = $this->deleteJson("/api/attachments/{$attachment->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('attachments', [
            'id' => $attachment->id,
            'path' => 'trash/'.$originalPath,
        ]);

        Storage::disk('local')->assertMissing($originalPath);
        Storage::disk('local')->assertExists('trash/'.$originalPath);
    }

    public function test_enterprise_admin_can_delete_attachment_moving_to_trash(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $ticket = $this->createTicket(['has_attachments' => true], $enterprise);

        $originalPath = "attachments/enterprise_{$enterprise->id}/ticket_{$ticket->id}/documento.pdf";
        Storage::disk('local')->put($originalPath, 'documento');

        $attachment = $this->createAttachment(['path' => $originalPath], $ticket);

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/attachments/{$attachment->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('attachments', [
            'id' => $attachment->id,
            'path' => 'trash/'.$originalPath,
        ]);
    }

    public function test_super_admin_can_delete_attachment_of_any_enterprise(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $superAdmin = $this->createSuperAdmin();
        $ticket = $this->createTicket(['has_attachments' => true], $enterprise);

        $originalPath = "attachments/enterprise_{$enterprise->id}/ticket_{$ticket->id}/doc_super.pdf";
        Storage::disk('local')->put($originalPath, 'conteudo');

        $attachment = $this->createAttachment(['path' => $originalPath], $ticket);

        Sanctum::actingAs($superAdmin);

        $response = $this->deleteJson("/api/attachments/{$attachment->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('attachments', [
            'id' => $attachment->id,
        ]);
    }

    public function test_user_cannot_delete_attachment_from_another_enterprise(): void
    {
        Storage::fake('local');

        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $userA = $this->createUser(['role' => 'admin'], $enterpriseA);
        $ticketB = $this->createTicket([], $enterpriseB);

        $originalPath = "attachments/enterprise_{$enterpriseB->id}/ticket_{$ticketB->id}/docB.pdf";
        Storage::disk('local')->put($originalPath, 'b');

        $attachmentB = $this->createAttachment(['path' => $originalPath], $ticketB);

        Sanctum::actingAs($userA);

        $response = $this->deleteJson("/api/attachments/{$attachmentB->id}");

        $response->assertForbidden();

        $this->assertNotSoftDeleted('attachments', [
            'id' => $attachmentB->id,
        ]);
    }

    public function test_regular_user_cannot_delete_attachment_of_another_user(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $user1 = $this->createUser(['role' => 'requester'], $enterprise);
        $user2 = $this->createUser(['role' => 'requester'], $enterprise);

        $ticket1 = $this->createTicket(['requester_id' => $user1->id], $enterprise);
        $originalPath = "attachments/enterprise_{$enterprise->id}/ticket_{$ticket1->id}/doc1.pdf";
        Storage::disk('local')->put($originalPath, '1');

        $attachment1 = $this->createAttachment(['path' => $originalPath], $ticket1);

        Sanctum::actingAs($user2);

        $response = $this->deleteJson("/api/attachments/{$attachment1->id}");

        $response->assertForbidden();
    }

    public function test_ticket_has_attachments_becomes_false_when_last_attachment_is_deleted(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $ticket = $this->createTicket(['has_attachments' => true], $enterprise);

        $originalPath = "attachments/enterprise_{$enterprise->id}/ticket_{$ticket->id}/unico.pdf";
        Storage::disk('local')->put($originalPath, 'conteudo');

        $attachment = $this->createAttachment(['path' => $originalPath], $ticket);

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/attachments/{$attachment->id}");

        $response->assertNoContent();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'has_attachments' => false,
        ]);
    }

    public function test_ticket_has_attachments_remains_true_when_other_attachments_still_exist(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $ticket = $this->createTicket(['has_attachments' => true], $enterprise);

        $path1 = "attachments/enterprise_{$enterprise->id}/ticket_{$ticket->id}/file1.pdf";
        $path2 = "attachments/enterprise_{$enterprise->id}/ticket_{$ticket->id}/file2.pdf";
        Storage::disk('local')->put($path1, '1');
        Storage::disk('local')->put($path2, '2');

        $attachment1 = $this->createAttachment(['path' => $path1], $ticket);
        $this->createAttachment(['path' => $path2], $ticket);

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/attachments/{$attachment1->id}");

        $response->assertNoContent();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'has_attachments' => true,
        ]);
    }
}
