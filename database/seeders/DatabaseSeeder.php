<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Enterprise;
use App\Models\Interaction;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\Sector;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Criar 100 Planos
        $this->command?->info('Seeding Plans...');
        $plans = [];
        for ($i = 1; $i <= 100; $i++) {
            $plans[] = [
                'name' => "Plano Tier {$i}",
                'max_users' => rand(5, 500),
                'max_tickets_per_month' => rand(50, 5000),
                'storage_mb' => rand(1024, 102400),
                'price' => rand(49, 999) + 0.90,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Plan::insert($plans);
        $allPlans = Plan::all();

        // 2. Criar 100 Empresas (CNPJ com 14 dígitos numéricos)
        $this->command?->info('Seeding Enterprises...');
        $enterprises = [];
        for ($i = 1; $i <= 100; $i++) {
            $cnpjFormatted = sprintf('%014d', $i);
            $enterprises[] = [
                'plan_id' => $allPlans->random()->id,
                'name' => "Empresa {$i} Ltda",
                'cnpj' => $cnpjFormatted,
                'email' => "contato_{$i}_".Str::random(4)."@empresa{$i}.com.br",
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Enterprise::insert($enterprises);
        $allEnterprises = Enterprise::all();

        // 3. Criar Usuários (com contas de teste fixas)
        $this->command?->info('Seeding Users...');
        $passwordHash = Hash::make('password');

        // Super Admin para testes no Postman
        User::create([
            'enterprise_id' => $allEnterprises->first()->id,
            'name' => 'Super Administrador',
            'email' => 'superadmin@harsudesk.com',
            'password' => $passwordHash,
            'role' => 'super_admin',
            'active' => true,
        ]);

        // Admin da Empresa 1 para testes no Postman
        User::create([
            'enterprise_id' => $allEnterprises->first()->id,
            'name' => 'Admin Empresa 1',
            'email' => 'admin@empresa1.com',
            'password' => $passwordHash,
            'role' => 'admin',
            'active' => true,
        ]);

        // Solicitante comum da Empresa 1 para testes no Postman
        User::create([
            'enterprise_id' => $allEnterprises->first()->id,
            'name' => 'Usuario Empresa 1',
            'email' => 'user@empresa1.com',
            'password' => $passwordHash,
            'role' => 'requester',
            'active' => true,
        ]);

        $users = [];
        $roles = ['admin', 'attendant', 'requester'];
        for ($i = 4; $i <= 100; $i++) {
            $users[] = [
                'enterprise_id' => $allEnterprises->random()->id,
                'name' => "Usuario {$i}",
                'email' => "usuario{$i}_".Str::random(3).'@harsudesk.com',
                'password' => $passwordHash,
                'role' => $roles[array_rand($roles)],
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        User::insert($users);
        $allUsers = User::all();

        // 4. Criar 100 Setores
        $this->command?->info('Seeding Sectors...');
        $sectorNames = ['Suporte TI', 'Financeiro', 'Recursos Humanos', 'Comercial', 'Logística', 'Atendimento', 'Infraestrutura', 'Desenvolvimento', 'Operações', 'Jurídico'];
        $sectors = [];
        for ($i = 1; $i <= 100; $i++) {
            $baseName = $sectorNames[array_rand($sectorNames)];
            $sectors[] = [
                'enterprise_id' => $allEnterprises->random()->id,
                'name' => "{$baseName} #{$i}",
                'active' => true,
                'accepts_tickets' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Sector::insert($sectors);
        $allSectors = Sector::all();

        // 5. Vincular Usuários aos Setores (pivot sector_user)
        $this->command?->info('Seeding Sector Users...');
        $sectorUsers = [];
        foreach ($allUsers as $user) {
            $enterpriseSectors = $allSectors->where('enterprise_id', $user->enterprise_id);
            if ($enterpriseSectors->isNotEmpty()) {
                $sectorUsers[] = [
                    'user_id' => $user->id,
                    'sector_id' => $enterpriseSectors->random()->id,
                ];
            }
        }
        DB::table('sector_user')->insertOrIgnore($sectorUsers);

        // 6. Criar 100 Chamados (Tickets)
        $this->command?->info('Seeding Tickets...');
        $statuses = ['open', 'in_progress', 'concluded', 'cancelled', 'reopened'];
        $priorities = ['low', 'medium', 'high'];
        $tickets = [];

        for ($i = 1; $i <= 100; $i++) {
            $enterprise = $allEnterprises->random();
            $enterpriseUsers = $allUsers->where('enterprise_id', $enterprise->id);
            $enterpriseSectors = $allSectors->where('enterprise_id', $enterprise->id);

            $requester = $enterpriseUsers->isNotEmpty() ? $enterpriseUsers->random() : $allUsers->first();
            $sector = $enterpriseSectors->isNotEmpty() ? $enterpriseSectors->random() : $allSectors->first();
            $attendant = (rand(0, 1) === 1 && $enterpriseUsers->isNotEmpty()) ? $enterpriseUsers->random() : null;

            $tickets[] = [
                'enterprise_id' => $enterprise->id,
                'sector_id' => $sector->id,
                'requester_id' => $requester->id,
                'attendant_id' => $attendant?->id,
                'title' => "Chamado de suporte #{$i} - ".Str::title(fake()->words(3, true)),
                'description' => "Descrição detalhada do chamado #{$i}: ".fake()->paragraph(),
                'status' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
                'has_attachments' => false,
                'created_at' => now()->subDays(rand(0, 30)),
                'updated_at' => now(),
            ];
        }
        Ticket::insert($tickets);
        $allTickets = Ticket::all();

        // 7. Criar 100 Interações (Interactions)
        $this->command?->info('Seeding Interactions...');
        $interactions = [];
        for ($i = 1; $i <= 100; $i++) {
            $ticket = $allTickets->random();
            $enterpriseUsers = $allUsers->where('enterprise_id', $ticket->enterprise_id);
            $user = $enterpriseUsers->isNotEmpty() ? $enterpriseUsers->random() : $allUsers->first();

            $interactions[] = [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'content' => "Interação #{$i} no chamado: ".fake()->sentence(),
                'has_attachments' => false,
                'created_at' => now()->subDays(rand(0, 20)),
                'updated_at' => now(),
            ];
        }
        Interaction::insert($interactions);
        $allInteractions = Interaction::all();

        // 8. Criar 100 Notificações (Notifications)
        $this->command?->info('Seeding Notifications...');
        $notifications = [];
        $types = ['ticket_created', 'ticket_updated', 'interaction_added', 'status_changed'];
        for ($i = 1; $i <= 100; $i++) {
            $user = $allUsers->random();
            $notifications[] = [
                'user_id' => $user->id,
                'message' => "Notificação #{$i}: O chamado #".rand(1, 100).' foi atualizado.',
                'link' => '/tickets/'.rand(1, 100),
                'type' => $types[array_rand($types)],
                'read' => (bool) rand(0, 1),
                'created_at' => now()->subDays(rand(0, 15)),
                'updated_at' => now(),
            ];
        }
        Notification::insert($notifications);

        // 9. Criar 100 Anexos (Attachments)
        $this->command?->info('Seeding Attachments...');
        $attachments = [];
        $mimeTypes = ['image/png', 'image/jpeg', 'application/pdf', 'text/plain'];
        for ($i = 1; $i <= 100; $i++) {
            $ticket = $allTickets->random();
            $interaction = rand(0, 1) ? $allInteractions->where('ticket_id', $ticket->id)->first() : null;

            $attachments[] = [
                'ticket_id' => $ticket->id,
                'interaction_id' => $interaction?->id,
                'original_name' => "documento_{$i}.pdf",
                'path' => "attachments/{$ticket->id}/doc_{$i}.pdf",
                'mime_type' => $mimeTypes[array_rand($mimeTypes)],
                'size_kb' => rand(50, 5120),
                'created_at' => now()->subDays(rand(0, 10)),
                'updated_at' => now(),
            ];
        }
        Attachment::insert($attachments);

        $this->command?->info('Database seeding completed successfully! (100 records per table)');
    }
}
