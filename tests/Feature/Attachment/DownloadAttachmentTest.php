<?php

namespace Tests\Feature\Attachment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class DownloadAttachmentTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_download_attachment(): void
    {
        $attachment = $this->createAttachment();

        $response = $this->getJson("/api/attachments/{$attachment->id}/download");

        $response->assertUnauthorized();
    }

    public function test_requester_can_download_their_ticket_attachment(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $requester = $this->createUser(['role' => 'requester'], $enterprise);
        $ticket = $this->createTicket(['requester_id' => $requester->id], $enterprise);

        $path = "attachments/enterprise_{$enterprise->id}/ticket_{$ticket->id}/relatorio.pdf";
        Storage::disk('local')->put($path, 'conteudo fake do arquivo');

        $attachment = $this->createAttachment([
            'original_name' => 'relatorio.pdf',
            'path' => $path,
        ], $ticket);

        Sanctum::actingAs($requester);

        $response = $this->get("/api/attachments/{$attachment->id}/download");

        $response->assertOk();
        $response->assertHeader('content-disposition', 'attachment; filename=relatorio.pdf');
    }

    public function test_enterprise_admin_can_download_attachment_from_their_enterprise(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $ticket = $this->createTicket([], $enterprise);

        $path = "attachments/enterprise_{$enterprise->id}/ticket_{$ticket->id}/comprovante.pdf";
        Storage::disk('local')->put($path, 'conteudo comprovante');

        $attachment = $this->createAttachment([
            'original_name' => 'comprovante.pdf',
            'path' => $path,
        ], $ticket);

        Sanctum::actingAs($admin);

        $response = $this->get("/api/attachments/{$attachment->id}/download");

        $response->assertOk();
        $response->assertHeader('content-disposition', 'attachment; filename=comprovante.pdf');
    }

    public function test_user_cannot_download_attachment_from_another_enterprise(): void
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

        $response = $this->getJson("/api/attachments/{$attachmentB->id}/download");

        $response->assertForbidden();
    }

    public function test_returns_404_when_file_not_found_on_storage(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $requester = $this->createUser(['role' => 'requester'], $enterprise);
        $ticket = $this->createTicket(['requester_id' => $requester->id], $enterprise);

        $attachment = $this->createAttachment([
            'path' => 'attachments/arquivo_inexistente.pdf',
        ], $ticket);

        Sanctum::actingAs($requester);

        $response = $this->getJson("/api/attachments/{$attachment->id}/download");

        $response->assertNotFound();
    }
}
