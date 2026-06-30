<?php

use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
    return (int) $user->id === $id;
});

Broadcast::channel('repair-request.{id}', function (User $user, int $id) {
    $repairRequest = RepairRequest::find($id);

    return $repairRequest && $repairRequest->hasChatParticipant($user);
});
