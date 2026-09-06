<?php

namespace App\Livewire\Components;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class NotificationBell extends Component
{
    public int $previousUnreadCount = 0;

    public function mount(): void
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $this->previousUnreadCount = $user->unreadNotifications()->count();
        }
    }

    public function markAsRead(string $id, ?string $redirectUrl = null)
    {
        if (! Auth::check()) {
            return null;
        }

        /** @var User $user */
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        if ($redirectUrl) {
            return $this->redirect($redirectUrl, navigate: true);
        }
    }

    public function markAllAsRead(): void
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();
        }
    }

    #[On('new-notification')]
    public function render()
    {
        if (! Auth::check()) {
            return view('livewire.components.notification-bell', [
                'unreadCount' => 0,
                'notifications' => collect(),
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        $unreadCount = $user->unreadNotifications()->count();

        // Check if new notification arrived -> emit toast event
        if ($unreadCount > $this->previousUnreadCount) {
            $latestNotif = $user->unreadNotifications()->latest()->first();
            if ($latestNotif) {
                $data = $latestNotif->data;
                $this->dispatch('new-notification', [
                    'title' => $data['title'] ?? 'Notifikasi Baru',
                    'message' => $data['message'] ?? '',
                ]);
            }
        }
        $this->previousUnreadCount = $unreadCount;

        $notifications = $user->notifications()->latest()->take(10)->get();

        return view('livewire.components.notification-bell', [
            'unreadCount' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }
}
