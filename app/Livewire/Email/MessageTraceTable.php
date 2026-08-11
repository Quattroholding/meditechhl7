<?php

namespace App\Livewire\Email;

use App\Services\ExchangeMessageTraceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class MessageTraceTable extends Component
{
    use WithPagination;

    public $searchTerm = '';

    public $recipientEmail = '';

    public $senderEmail = '';

    public $startDate = '';

    public $endDate = '';

    public $perPage = 50;

    public $selectedMessage = null;

    public $showDetailModal = false;

    protected $listeners = ['refreshMessages' => '$refresh'];

    public function mount()
    {
        // Por defecto, últimas 24 horas
        $this->senderEmail = config('services.microsoft.mailbox_email', 'notificaciones@meditecpty.com');
        $this->startDate = Carbon::now()->subDay()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedRecipientEmail()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function search()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->searchTerm = '';
        $this->recipientEmail = '';
        $this->startDate = Carbon::now()->subDay()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->resetPage();
    }

    public function viewDetails($messageIndex)
    {
        try {
            $traceService = new ExchangeMessageTraceService;

            $startDate = $this->startDate ? Carbon::parse($this->startDate) : Carbon::now()->subDay();
            $endDate = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : Carbon::now();

            $result = $traceService->getMessageTrace(
                senderAddress: $this->senderEmail ?: null,
                recipientAddress: $this->recipientEmail ?: null,
                subject: $this->searchTerm ?: null,
                startDate: $startDate,
                endDate: $endDate,
                limit: 1000
            );

            if ($result['success'] && isset($result['messages'][$messageIndex])) {
                $this->selectedMessage = $result['messages'][$messageIndex];
                $this->showDetailModal = true;
            }
        } catch (Exception $e) {
            session()->flash('error', 'Error al obtener detalles: '.$e->getMessage());
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedMessage = null;
    }

    public function render()
    {
        try {
            $traceService = new ExchangeMessageTraceService;

            $startDate = $this->startDate ? Carbon::parse($this->startDate) : Carbon::now()->subDay();
            $endDate = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : Carbon::now();

            $result = $traceService->getMessageTrace(
                senderAddress: $this->senderEmail ?: null,
                recipientAddress: $this->recipientEmail ?: null,
                subject: $this->searchTerm ?: null,
                startDate: $startDate,
                endDate: $endDate,
                limit: 1000
            );

            $messages = $result['messages'] ?? [];
            $totalCount = $result['total_count'] ?? 0;

            // Paginación manual
            $currentPage = $this->getPage();
            $offset = ($currentPage - 1) * $this->perPage;
            $paginatedMessages = array_slice($messages, $offset, $this->perPage);

            $paginator = new LengthAwarePaginator(
                $paginatedMessages,
                $totalCount,
                $this->perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('livewire.email.message-trace-table', [
                'messages' => $paginator,
                'isConnected' => $result['success'] ?? false,
                'errorMessage' => $result['error'] ?? null,
                'source' => $result['source'] ?? 'unknown',
            ]);
        } catch (Exception $e) {
            return view('livewire.email.message-trace-table', [
                'messages' => new LengthAwarePaginator([], 0, $this->perPage, 1),
                'isConnected' => false,
                'errorMessage' => $e->getMessage(),
                'source' => 'error',
            ]);
        }
    }
}
