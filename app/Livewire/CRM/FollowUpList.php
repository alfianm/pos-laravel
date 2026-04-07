<?php

namespace App\Livewire\CRM;

use App\Models\CustomerTimeline;
use App\Models\FollowUp;
use Livewire\Component;
use Livewire\WithPagination;

class FollowUpList extends Component
{
    use WithPagination;

    public $leadId;

    public $type = 'call';

    public $content;

    public $scheduled_at;

    public $notes;

    public $showForm = false;

    public $is_recurring = false;

    public $recurrence_type;

    public $recurrence_interval = 1;

    public $recurrence_end_date;

    public $reminder_minutes_before;

    public $filter = 'all';

    public $completeFollowUpId = null;

    public $completeNotes = '';

    protected function rules()
    {
        return [
            'type' => 'required|in:call,email,chat,meet',
            'content' => 'required|min:5',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
            'is_recurring' => 'boolean',
            'recurrence_type' => 'required_if:is_recurring,true|nullable|in:daily,weekly,monthly',
            'recurrence_interval' => 'required_if:is_recurring,true|nullable|integer|min:1',
            'recurrence_end_date' => 'nullable|date|after:scheduled_at',
            'reminder_minutes_before' => 'nullable|integer|in:5,10,15,30,60',
        ];
    }

    protected $validationAttributes = [
        'type' => 'tipe follow-up',
        'content' => 'isi kegiatan',
        'scheduled_at' => 'jadwal',
        'notes' => 'catatan',
        'is_recurring' => 'berulang',
        'recurrence_type' => 'tipe pengulangan',
        'recurrence_interval' => 'interval pengulangan',
        'recurrence_end_date' => 'tanggal akhir pengulangan',
        'reminder_minutes_before' => 'pengingat',
    ];

    public function mount($leadId)
    {
        $this->leadId = $leadId;
        $this->scheduled_at = now()->addHour()->format('Y-m-d\TH:i');
    }

    public function toggleForm()
    {
        $this->showForm = ! $this->showForm;
        if (! $this->showForm) {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        $this->reset(['type', 'content', 'scheduled_at', 'notes', 'is_recurring', 'recurrence_type', 'recurrence_interval', 'recurrence_end_date', 'reminder_minutes_before']);
        $this->type = 'call';
        $this->scheduled_at = now()->addHour()->format('Y-m-d\TH:i');
    }

    public function saveFollowUp()
    {
        $this->validate();

        $followUp = FollowUp::create([
            'tenant_id' => auth()->user()->tenant_id,
            'lead_id' => $this->leadId,
            'performed_by' => auth()->id(),
            'type' => $this->type,
            'scheduled_at' => $this->scheduled_at,
            'notes' => $this->content,
            'status' => 'pending',
            'is_recurring' => $this->is_recurring,
            'recurrence_type' => $this->is_recurring ? $this->recurrence_type : null,
            'recurrence_interval' => $this->is_recurring ? $this->recurrence_interval : null,
            'recurrence_end_date' => $this->is_recurring ? $this->recurrence_end_date : null,
            'reminder_minutes_before' => $this->reminder_minutes_before,
        ]);

        CustomerTimeline::create([
            'tenant_id' => auth()->user()->tenant_id,
            'lead_id' => $this->leadId,
            'event_type' => 'follow_up_scheduled',
            'title' => 'Follow-up dijadwalkan: '.ucfirst($this->type),
            'description' => $this->content.($this->is_recurring ? ' (Berulang)' : ''),
            'reference_id' => $followUp->id,
            'reference_type' => FollowUp::class,
        ]);

        $this->resetForm();
        $this->showForm = false;
        $this->dispatch('refreshTimeline');
        session()->flash('success', 'Follow-up berhasil dijadwalkan!');
    }

    public function openCompleteModal($followUpId)
    {
        $this->completeFollowUpId = $followUpId;
        $this->completeNotes = '';
    }

    public function completeFollowUp()
    {
        $followUp = FollowUp::findOrFail($this->completeFollowUpId);

        $followUp->markAsCompleted($this->completeNotes);

        CustomerTimeline::create([
            'tenant_id' => auth()->user()->tenant_id,
            'lead_id' => $this->leadId,
            'event_type' => 'follow_up_completed',
            'title' => 'Follow-up selesai: '.ucfirst($followUp->type),
            'description' => $followUp->notes,
            'reference_id' => $followUp->id,
            'reference_type' => FollowUp::class,
        ]);

        $this->completeFollowUpId = null;
        $this->completeNotes = '';

        $this->dispatch('refreshTimeline');
        session()->flash('success', 'Follow-up berhasil diselesaikan!');
    }

    public function cancelFollowUp($followUpId)
    {
        $followUp = FollowUp::findOrFail($followUpId);
        $followUp->cancel();

        CustomerTimeline::create([
            'tenant_id' => auth()->user()->tenant_id,
            'lead_id' => $this->leadId,
            'event_type' => 'follow_up_cancelled',
            'title' => 'Follow-up dibatalkan: '.ucfirst($followUp->type),
            'description' => 'Follow-up dibatalkan',
            'reference_id' => $followUp->id,
            'reference_type' => FollowUp::class,
        ]);

        $this->dispatch('refreshTimeline');
        session()->flash('success', 'Follow-up dibatalkan.');
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function render()
    {
        $query = FollowUp::with('performer')
            ->where('lead_id', $this->leadId);

        match ($this->filter) {
            'pending' => $query->where('status', 'pending'),
            'overdue' => $query->where('status', 'pending')->where('scheduled_at', '<', now()),
            'completed' => $query->where('status', 'completed'),
            'cancelled' => $query->where('status', 'cancelled'),
            default => null,
        };

        $followUps = $query->orderBy('scheduled_at', 'asc')->paginate(10);

        return view('livewire.c-r-m.follow-up-list', [
            'followUps' => $followUps,
        ]);
    }
}
