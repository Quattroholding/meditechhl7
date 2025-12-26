<?php

namespace App\Livewire\Notification;

use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = 'all'; // all, unread, read

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $pagination = 25;

    protected $listeners = ['notificationMarkedAsRead' => '$refresh'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notificationMarkedAsRead');
            session()->flash('message', 'Notificación marcada como leída');
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->dispatch('notificationMarkedAsRead');
        session()->flash('message', 'Todas las notificaciones marcadas como leídas');
    }

    public function render()
    {
        $notifications = auth()->user()
            ->notifications()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereRaw("JSON_EXTRACT(data, '$.title') LIKE ?", ['%'.$this->search.'%'])
                        ->orWhereRaw("JSON_EXTRACT(data, '$.message') LIKE ?", ['%'.$this->search.'%']);
                });
            })
            ->when($this->filterStatus === 'unread', function ($query) {
                $query->whereNull('read_at');
            })
            ->when($this->filterStatus === 'read', function ($query) {
                $query->whereNotNull('read_at');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.notification.data-table', [
            'notifications' => $notifications,
        ]);
    }
}
