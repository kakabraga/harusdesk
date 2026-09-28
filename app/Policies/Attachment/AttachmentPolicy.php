<?php

namespace App\Policies\Attachment;

use App\Models\Attachment;
use App\Models\User;

class AttachmentPolicy
{
    /**
     * Determine whether the user can view any attachments.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view/access the attachment metadata.
     */
    public function view(User $user, Attachment $attachment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $enterpriseId = $attachment->ticket?->enterprise_id
            ?? $attachment->interaction?->ticket?->enterprise_id;

        if ((int) $user->enterprise_id !== (int) $enterpriseId) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $ticket = $attachment->ticket ?? $attachment->interaction?->ticket;

        if ($ticket && $ticket->requester_id === $user->id) {
            return true;
        }

        if ($ticket && $user->sectors()->where('sectors.id', $ticket->sector_id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can download or preview the attachment file.
     */
    public function download(User $user, Attachment $attachment): bool
    {
        return $this->view($user, $attachment);
    }

    /**
     * Determine whether the user can delete the attachment.
     */
    public function delete(User $user, Attachment $attachment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $enterpriseId = $attachment->ticket?->enterprise_id
            ?? $attachment->interaction?->ticket?->enterprise_id;

        if ((int) $user->enterprise_id !== (int) $enterpriseId) {
            return false;
        }

        $ticket = $attachment->ticket ?? $attachment->interaction?->ticket;

        return $user->isAdmin() || ($ticket && $ticket->requester_id === $user->id);
    }

    public function forceDelete(User $user, Attachment $attachment): bool
    {
        return $user->isSuperAdmin();
    }
}
