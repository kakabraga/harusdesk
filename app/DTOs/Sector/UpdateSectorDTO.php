<?php

namespace App\DTOs\Sector;


class UpdateSectorDTO
{

    public function __construct(
        public readonly ?string $name,
        public readonly ?bool $active
    ) {

    }

    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null)
            $data['name'] = $this->name;
        if ($this->active !== null)
            $data['active'] = $this->active;

        return $data;
    }
}
