<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class EventList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $confirmingDelete = false;
    public $eventToDelete = null;

    protected $queryString = ['search', 'status', 'sortField', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
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
    }

    public function confirmDelete($eventId)
    {
        $this->confirmingDelete = true;
        $this->eventToDelete = $eventId;
    }

    public function deleteEvent()
    {
        if ($this->eventToDelete) {
            $event = Event::find($this->eventToDelete);

            if ($event) {
                $event->delete();
                session()->flash('message', 'Event deleted successfully.');
            }
        }

        $this->confirmingDelete = false;
        $this->eventToDelete = null;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->eventToDelete = null;
    }

    public function toggleStatus($eventId)
    {
        $event = Event::find($eventId);

        if ($event) {
            $event->is_active = !$event->is_active;
            $event->save();
            session()->flash('message', 'Event status updated successfully.');
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Check if user has customer relationship
        if (!$user || !$user->customer) {
            // Return empty paginated result to maintain compatibility with view
            $emptyEvents = Event::with(['category', 'subCategory', 'customer', 'guests'])
                ->whereRaw('1=0') // This will return no results
                ->paginate(10);

            return view('livewire.events.event-list', [
                'events' => $emptyEvents,
            ]);
        }

        $query = Event::with(['category', 'subCategory', 'customer', 'guests'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('location', 'like', '%' . $this->search . '%');
                });
            })
            ->where('customer_id', $user->customer->id)
            ->orderBy($this->sortField, $this->sortDirection);

        $events = $query->paginate(10);

        return view('livewire.events.event-list', [
            'events' => $events,
        ]);
    }
}
