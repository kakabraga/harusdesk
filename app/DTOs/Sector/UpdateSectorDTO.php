<?php

namespace App\DTOs\Sector;

class UpdateSectorDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?bool $active = null,
        public readonly ?bool $acceptsTickets = null
    ) {}

    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->active !== null) {
            $data['active'] = $this->active;
        }

        if ($this->acceptsTickets !== null) {
            $data['accepts_tickets'] = $this->acceptsTickets;
        }

        return $data;
    }
}
