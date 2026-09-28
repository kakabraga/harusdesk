<?php

namespace Tests\Feature\Attachment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class ForceDeleteAttachmentTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_force_delete_attachment(): void
    {
        $attachment = $this->createAttachment();

        $response = $this->deleteJson("/api/attachments/{$attachment->id}/force");

        $response->assertUnauthorized();
    }

    public function test_regular_user_and_enterprise_admin_cannot_force_delete_attachment(): void
    {
        $enterprise = $this->createEnterprise();
        $admin = $this->createUser(['role' => 'admin'], $enterprise);
        $user = $this->createUser(['role' => 'requester'], $enterprise);

        $ticket = $this->createTicket([], $enterprise);
        $attachment = $this->createAttachment([], $ticket);

        Sanctum::actingAs($admin);
        $responseAdmin = $this->deleteJson("/api/attachments/{$attachment->id}/force");
        $responseAdmin->assertForbidden();

        Sanctum::actingAs($user);
        $responseUser = $this->deleteJson("/api/attachments/{$attachment->id}/force");
        $responseUser->assertForbidden();
    }

    public function test_super_admin_can_force_delete_soft_deleted_attachment_and_remove_file_from_storage(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $superAdmin = $this->createSuperAdmin();
        $ticket = $this->createTicket([], $enterprise);

        $trashPath = "trash/attachments/enterprise_{$enterprise->id}/ticket_{$ticket->id}/arquivo.pdf";
        Storage::disk('local')->put($trashPath, 'arquivo na lixeira');

        $attachment = $this->createAttachment([
            'path' => $trashPath,
            'deleted_at' => now(),
        ], $ticket);

        Sanctum::actingAs($superAdmin);

        $response = $this->deleteJson("/api/attachments/{$attachment->id}/force");

        $response->assertNoContent();

        $this->assertDatabaseMissing('attachments', [
            'id' => $attachment->id,
        ]);

        Storage::disk('local')->assertMissing($trashPath);
    }

    public function test_super_admin_can_force_delete_even_if_file_missing_from_storage(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $superAdmin = $this->createSuperAdmin();
        $ticket = $this->createTicket([], $enterprise);

        $attachment = $this->createAttachment([
            'path' => 'trash/arquivo_fantasma.pdf',
            'deleted_at' => now(),
        ], $ticket);

        Sanctum::actingAs($superAdmin);

        $response = $this->deleteJson("/api/attachments/{$attachment->id}/force");

        $response->assertNoContent();

        $this->assertDatabaseMissing('attachments', [
            'id' => $attachment->id,
        ]);
    }
}
