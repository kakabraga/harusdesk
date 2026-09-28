<?php

namespace App\Actions\Ticket;

use App\Actions\Attachments\AttachFilesToTicketAction;
use App\DTOs\Ticket\CreateTicketDTO;
use App\Models\Ticket;
use App\Repositories\Eloquent\Ticket\TicketRepositoryInterface;

class CreateTicketAction
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository,
        private AttachFilesToTicketAction $attachFilesToTicket
    ) {}

    public function execute(CreateTicketDTO $dto): Ticket
    {

        $ticket = $this->ticketRepository->create($dto);

        if (! empty($dto->attachments)) {
            $this->attachFilesToTicket->execute($dto->attachments, $ticket);
            $ticket->load('attachments');
        }

        return $ticket;
    }
}
