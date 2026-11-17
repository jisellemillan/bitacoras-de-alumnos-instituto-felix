<?php

namespace App\Livewire\Layout;

use Livewire\Component;

class NotificationBell extends Component
{
    public function getNotificationsProperty()
    {
        return auth()->user()->unreadNotifications;
    }

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.layout.notification-bell');
    }
}