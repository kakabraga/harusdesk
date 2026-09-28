<?php

namespace Tests\Feature\Ticket;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\InteractsWithTestData;

class CreateTicketTest extends TestCase
{
    use InteractsWithTestData;
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_create_ticket(): void
    {
        $response = $this->postJson('/api/tickets', [
            'title' => 'Problema na impressora',
            'description' => 'Impressora não liga.',
            'sector_id' => 1,
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_can_create_ticket_in_their_enterprise(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterprise);
        $sector = $this->createSector(['active' => true, 'accepts_tickets' => true], $enterprise);

        Sanctum::actingAs($user);

        $payload = [
            'sector_id' => $sector->id,
            'title' => 'Sem internet no setor financeiro',
            'description' => 'Todos os computadores da sala 3 estão sem conexão.',
            'priority' => 'high',
        ];

        $response = $this->postJson('/api/tickets', $payload);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Sem internet no setor financeiro')
            ->assertJsonPath('data.priority', 'high')
            ->assertJsonPath('data.status', 'open')
            ->assertJsonPath('data.enterprise_id', $enterprise->id)
            ->assertJsonPath('data.requester_id', $user->id)
            ->assertJsonPath('data.sector_id', $sector->id);

        $this->assertDatabaseHas('tickets', [
            'title' => 'Sem internet no setor financeiro',
            'enterprise_id' => $enterprise->id,
            'requester_id' => $user->id,
            'sector_id' => $sector->id,
            'priority' => 'high',
            'status' => 'open',
        ]);
    }

    public function test_super_admin_can_create_ticket_by_providing_enterprise_id(): void
    {
        $enterprise = $this->createEnterprise();
        $sector = $this->createSector(['active' => true, 'accepts_tickets' => true], $enterprise);
        $superAdmin = $this->createSuperAdmin();

        Sanctum::actingAs($superAdmin);

        $payload = [
            'enterprise_id' => $enterprise->id,
            'sector_id' => $sector->id,
            'title' => 'Ticket via SuperAdmin',
            'description' => 'Abertura de chamado direto pelo Super Admin do sistema.',
            'priority' => 'medium',
        ];

        $response = $this->postJson('/api/tickets', $payload);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.enterprise_id', $enterprise->id);

        $this->assertDatabaseHas('tickets', [
            'title' => 'Ticket via SuperAdmin',
            'enterprise_id' => $enterprise->id,
            'sector_id' => $sector->id,
        ]);
    }

    public function test_regular_user_cannot_provide_enterprise_id_in_payload(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterpriseA);
        $sector = $this->createSector(['active' => true, 'accepts_tickets' => true], $enterpriseA);

        Sanctum::actingAs($user);

        $payload = [
            'enterprise_id' => $enterpriseB->id,
            'sector_id' => $sector->id,
            'title' => 'Tentativa de burlar tenant',
            'description' => 'Descrição do chamado.',
        ];

        $response = $this->postJson('/api/tickets', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['enterprise_id']);
    }

    public function test_super_admin_must_provide_enterprise_id(): void
    {
        $enterprise = $this->createEnterprise();
        $sector = $this->createSector(['active' => true, 'accepts_tickets' => true], $enterprise);
        $superAdmin = $this->createSuperAdmin();

        Sanctum::actingAs($superAdmin);

        $payload = [
            'sector_id' => $sector->id,
            'title' => 'Sem enterprise id',
            'description' => 'Superadmin esqueceu de passar enterprise_id.',
        ];

        $response = $this->postJson('/api/tickets', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['enterprise_id']);
    }

    public function test_cannot_create_ticket_with_sector_from_another_enterprise(): void
    {
        $enterpriseA = $this->createEnterprise();
        $enterpriseB = $this->createEnterprise();
        $userA = $this->createUser(['role' => 'requester'], $enterpriseA);
        $sectorB = $this->createSector(['active' => true, 'accepts_tickets' => true], $enterpriseB);

        Sanctum::actingAs($userA);

        $payload = [
            'sector_id' => $sectorB->id,
            'title' => 'Setor de outra empresa',
            'description' => 'Tentando abrir chamado no setor da Empresa B.',
        ];

        $response = $this->postJson('/api/tickets', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sector_id']);
    }

    public function test_cannot_create_ticket_in_inactive_sector(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterprise);
        $inactiveSector = $this->createSector(['active' => false, 'accepts_tickets' => true], $enterprise);

        Sanctum::actingAs($user);

        $payload = [
            'sector_id' => $inactiveSector->id,
            'title' => 'Setor inativo',
            'description' => 'Tentando abrir chamado em setor inativo.',
        ];

        $response = $this->postJson('/api/tickets', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sector_id']);
    }

    public function test_cannot_create_ticket_in_sector_that_does_not_accept_tickets(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterprise);
        $sectorNoTickets = $this->createSector(['active' => true, 'accepts_tickets' => false], $enterprise);

        Sanctum::actingAs($user);

        $payload = [
            'sector_id' => $sectorNoTickets->id,
            'title' => 'Setor sem tickets',
            'description' => 'Tentando abrir chamado em setor com accepts_tickets = false.',
        ];

        $response = $this->postJson('/api/tickets', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sector_id']);
    }

    public function test_creation_fails_when_required_fields_are_missing(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterprise);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/tickets', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sector_id', 'title', 'description']);
    }

    public function test_creation_fails_when_priority_is_invalid(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterprise);
        $sector = $this->createSector([], $enterprise);

        Sanctum::actingAs($user);

        $payload = [
            'sector_id' => $sector->id,
            'title' => 'Prioridade invalida',
            'description' => 'Descrição do chamado.',
            'priority' => 'urgente_demais',
        ];

        $response = $this->postJson('/api/tickets', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['priority']);
    }

    public function test_user_can_create_ticket_with_multiple_attachments(): void
    {
        Storage::fake('local');

        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterprise);
        $sector = $this->createSector(['active' => true, 'accepts_tickets' => true], $enterprise);

        Sanctum::actingAs($user);

        $filePdf = UploadedFile::fake()->create('documento.pdf', 250, 'application/pdf');
        $fileImg = UploadedFile::fake()->create('print_erro.png', 300, 'image/png');

        $payload = [
            'sector_id' => $sector->id,
            'title' => 'Chamado com anexos',
            'description' => 'Segue documentos em anexo para analise.',
            'priority' => 'high',
            'attachments' => [$filePdf, $fileImg],
        ];

        $response = $this->post('/api/tickets', $payload, ['Accept' => 'application/json']);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Chamado com anexos')
            ->assertJsonPath('data.has_attachments', true)
            ->assertJsonCount(2, 'data.attachments')
            ->assertJsonPath('data.attachments.0.original_name', 'documento.pdf')
            ->assertJsonPath('data.attachments.1.original_name', 'print_erro.png');

        $ticketId = $response->json('data.id');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticketId,
            'title' => 'Chamado com anexos',
            'has_attachments' => true,
        ]);

        $this->assertDatabaseHas('attachments', [
            'ticket_id' => $ticketId,
            'original_name' => 'documento.pdf',
            'mime_type' => 'application/pdf',
        ]);

        $this->assertDatabaseHas('attachments', [
            'ticket_id' => $ticketId,
            'original_name' => 'print_erro.png',
            'mime_type' => 'image/png',
        ]);

        $pdfAttachment = $response->json('data.attachments.0');
        $imgAttachment = $response->json('data.attachments.1');

        Storage::disk('local')->assertExists($pdfAttachment['path']);
        Storage::disk('local')->assertExists($imgAttachment['path']);
    }

    public function test_user_can_create_ticket_without_attachments(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterprise);
        $sector = $this->createSector(['active' => true, 'accepts_tickets' => true], $enterprise);

        Sanctum::actingAs($user);

        $payload = [
            'sector_id' => $sector->id,
            'title' => 'Chamado sem anexos',
            'description' => 'Descricao sem nenhum anexo.',
        ];

        $response = $this->postJson('/api/tickets', $payload);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonMissingPath('data.attachments');

        $ticketId = $response->json('data.id');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticketId,
            'has_attachments' => false,
        ]);

        $this->assertDatabaseCount('attachments', 0);
    }

    public function test_creation_fails_when_attachment_has_invalid_mime_type(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterprise);
        $sector = $this->createSector(['active' => true, 'accepts_tickets' => true], $enterprise);

        Sanctum::actingAs($user);

        $invalidFile = UploadedFile::fake()->create('script.sh', 10, 'text/x-shellscript');

        $payload = [
            'sector_id' => $sector->id,
            'title' => 'Chamado com arquivo invalido',
            'description' => 'Tentativa de enviar script.',
            'attachments' => [$invalidFile],
        ];

        $response = $this->post('/api/tickets', $payload, ['Accept' => 'application/json']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['attachments.0']);
    }

    public function test_creation_fails_when_attachment_exceeds_max_size(): void
    {
        $enterprise = $this->createEnterprise();
        $user = $this->createUser(['role' => 'requester'], $enterprise);
        $sector = $this->createSector(['active' => true, 'accepts_tickets' => true], $enterprise);

        Sanctum::actingAs($user);

        // 12000 KB > 10240 KB (10MB)
        $oversizedFile = UploadedFile::fake()->create('grande.pdf', 12000, 'application/pdf');

        $payload = [
            'sector_id' => $sector->id,
            'title' => 'Chamado com arquivo muito grande',
            'description' => 'Tentativa de enviar arquivo maior que 10MB.',
            'attachments' => [$oversizedFile],
        ];

        $response = $this->post('/api/tickets', $payload, ['Accept' => 'application/json']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['attachments.0']);
    }
}
