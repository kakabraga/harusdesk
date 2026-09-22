<?php

namespace Tests\Traits;

use App\Models\Enterprise;
use App\Models\Plan;
use App\Models\Sector;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait InteractsWithTestData
{
    /**
     * Create a Plan fixture.
     */
    protected function createPlan(array $attributes = []): Plan
    {
        return Plan::create(array_merge([
            'name' => 'Plano Padrão '.Str::random(5),
            'max_users' => 10,
            'max_tickets_per_month' => 100,
            'storage_mb' => 1024,
            'price' => 99.90,
            'active' => true,
        ], $attributes));
    }

    /**
     * Create an Enterprise fixture.
     */
    protected function createEnterprise(array $attributes = []): Enterprise
    {
        $planId = $attributes['plan_id'] ?? $this->createPlan()->id;

        return Enterprise::create(array_merge([
            'plan_id' => $planId,
            'name' => 'Empresa '.Str::random(5),
            'cnpj' => sprintf('%014d', mt_rand(1, 99999999999999)),
            'email' => 'enterprise_'.Str::random(8).'@teste.com',
            'active' => true,
        ], $attributes));
    }

    /**
     * Create a User fixture with a specified role.
     */
    protected function createUser(array $attributes = [], ?Enterprise $enterprise = null): User
    {
        $enterpriseId = $attributes['enterprise_id'] ?? ($enterprise ? $enterprise->id : $this->createEnterprise()->id);

        return User::create(array_merge([
            'enterprise_id' => $enterpriseId,
            'name' => 'Usuário '.Str::random(5),
            'email' => 'user_'.Str::random(8).'@teste.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'active' => true,
        ], $attributes));
    }

    /**
     * Create a SuperAdmin user fixture.
     */
    protected function createSuperAdmin(array $attributes = []): User
    {
        return $this->createUser(array_merge([
            'role' => 'super_admin',
        ], $attributes));
    }

    /**
     * Create a Sector fixture.
     */
    protected function createSector(array $attributes = [], ?Enterprise $enterprise = null): Sector
    {
        $enterpriseId = $attributes['enterprise_id'] ?? ($enterprise ? $enterprise->id : $this->createEnterprise()->id);

        return Sector::create(array_merge([
            'enterprise_id' => $enterpriseId,
            'name' => 'Setor '.Str::random(5),
            'active' => true,
            'accepts_tickets' => true,
        ], $attributes));
    }

    /**
     * Create a Ticket fixture.
     */
    protected function createTicket(array $attributes = [], ?Enterprise $enterprise = null): Ticket
    {
        $enterprise = $enterprise ?? $this->createEnterprise();
        $sectorId = $attributes['sector_id'] ?? $this->createSector([], $enterprise)->id;
        $requesterId = $attributes['requester_id'] ?? $this->createUser(['role' => 'requester'], $enterprise)->id;

        return Ticket::create(array_merge([
            'enterprise_id' => $enterprise->id,
            'sector_id' => $sectorId,
            'requester_id' => $requesterId,
            'attendant_id' => null,
            'title' => 'Chamado de Teste '.Str::random(5),
            'description' => 'Descrição do chamado de teste para validações.',
            'status' => 'open',
            'priority' => 'low',
            'has_attachments' => false,
        ], $attributes));
    }
}
