<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\TechnicianApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * Notification feed for the navbar panel (chat + admin alerts).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = Message::notificationThreadsForUser($user, 8)
            ->map(function (array $thread) {
                $repair = $thread['repair_request'];
                $message = $thread['latest_message'];

                return [
                    'type' => 'chat',
                    'id' => 'chat-'.$repair->id,
                    'title' => $repair->reference,
                    'subtitle' => trim("{$repair->device_type} · {$repair->brand}", ' ·'),
                    'preview' => Str::limit($message->body, 100),
                    'sender' => $message->sender->name,
                    'unread_count' => $thread['unread_count'],
                    'url' => route('repair-requests.show', $repair),
                    'time' => $message->created_at->diffForHumans(),
                    'timestamp' => $message->created_at->timestamp,
                ];
            });

        if ($user->isAdmin()) {
            $applications = TechnicianApplication::query()
                ->with('applicant')
                ->where('status', TechnicianApplication::STATUS_PENDING)
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn (TechnicianApplication $application) => [
                    'type' => 'application',
                    'id' => 'app-'.$application->id,
                    'title' => 'Technician application',
                    'subtitle' => $application->applicant->name,
                    'preview' => Str::limit($application->motivation, 100),
                    'sender' => $application->applicant->name,
                    'unread_count' => 1,
                    'url' => route('technician-applications.show', $application),
                    'time' => $application->created_at->diffForHumans(),
                    'timestamp' => $application->created_at->timestamp,
                ]);

            $notifications = $notifications
                ->concat($applications)
                ->sortByDesc('timestamp')
                ->values();
        }

        $chatUnread = Message::unreadCountForUser($user);
        $pendingApplications = $user->isAdmin()
            ? TechnicianApplication::where('status', TechnicianApplication::STATUS_PENDING)->count()
            : 0;

        return response()->json([
            'total' => $chatUnread + $pendingApplications,
            'chat_unread' => $chatUnread,
            'pending_applications' => $pendingApplications,
            'notifications' => $notifications->values(),
        ]);
    }
}
