<?php

namespace Tests\Feature\Attachment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class PreviewAttachmentTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_preview_attachment(): void
    {
        $attachment = $this->createAttachment();

        $response = $this->getJson("/api/attachments/{$attachment->id}/preview");

        $response->assertUnauthorized();
    }

    public function test_requester_can_preview_their_ticket_attachment(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $requester = $this->createUser(['role' => 'requester'], $enterprise);
        $ticket = $this->createTicket(['requester_id' => $requester->id], $enterprise);

        $path = "attachments/enterprise_{$enterprise->id}/ticket_{$ticket->id}/preview.pdf";
        Storage::disk('local')->put($path, 'conteudo para preview');

        $attachment = $this->createAttachment([
            'original_name' => 'preview.pdf',
            'path' => $path,
        ], $ticket);

        Sanctum::actingAs($requester);

        $response = $this->get("/api/attachments/{$attachment->id}/preview");

        $response->assertOk();
        $response->assertHeader('content-disposition', 'inline; filename=preview.pdf');
    }

    public function test_enterprise_admin_can_preview_attachment_from_their_enterprise(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $ticket = $this->createTicket([], $enterprise);

        $path = "attachments/enterprise_{$enterprise->id}/ticket_{$ticket->id}/imagem.png";
        Storage::disk('local')->put($path, 'imagem preview');

        $attachment = $this->createAttachment([
            'original_name' => 'imagem.png',
            'path' => $path,
        ], $ticket);

        Sanctum::actingAs($admin);

        $response = $this->get("/api/attachments/{$attachment->id}/preview");

        $response->assertOk();
        $response->assertHeader('content-disposition', 'inline; filename=imagem.png');
    }

    public function test_user_cannot_preview_attachment_from_another_enterprise(): void
    {
        Storage::fake('local');

        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();

        $userA = $this->createUser(['role' => 'requester'], $enterpriseA);
        $ticketB = $this->createTicket([], $enterpriseB);

        $path = "attachments/enterprise_{$enterpriseB->id}/ticket_{$ticketB->id}/docB.pdf";
        Storage::disk('local')->put($path, 'conteudo b');

        $attachmentB = $this->createAttachment(['path' => $path], $ticketB);

        Sanctum::actingAs($userA);

        $response = $this->getJson("/api/attachments/{$attachmentB->id}/preview");

        $response->assertForbidden();
    }

    public function test_returns_404_when_preview_file_not_found_on_storage(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $requester = $this->createUser(['role' => 'requester'], $enterprise);
        $ticket = $this->createTicket(['requester_id' => $requester->id], $enterprise);

        $attachment = $this->createAttachment([
            'path' => 'attachments/inexistente_preview.pdf',
        ], $ticket);

        Sanctum::actingAs($requester);

        $response = $this->getJson("/api/attachments/{$attachment->id}/preview");

        $response->assertNotFound();
    }
}
