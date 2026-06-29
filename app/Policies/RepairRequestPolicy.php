<?php

namespace App\Policies;

use App\Models\RepairRequest;
use App\Models\User;

class RepairRequestPolicy
{
    /**
     * Whether the user may view or post in this repair request's chat thread.
     */
    public function participateInChat(User $user, RepairRequest $repairRequest): bool
    {
        return $repairRequest->hasChatParticipant($user);
    }
}
