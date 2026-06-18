<?php

namespace App\Livewire\Guests;

use App\Models\Guest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class GuestList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $confirmingDelete = false;
    public $guestToDelete = null;

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    // Reset pagination when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /** 
     * Sort guests by a given field 
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    /** 
     * Show confirmation modal before deleting 
     */
    public function confirmDelete($guestId)
    {
        $this->confirmingDelete = true;
        $this->guestToDelete = $guestId;
    }

    /** 
     * Delete a guest record 
     */
    public function deleteGuest()
    {
        if ($this->guestToDelete) {
            $guest = Guest::find($this->guestToDelete);

            if ($guest) {
                $guest->delete();
                session()->flash('message', 'Guest deleted successfully.');
            }
        }

        $this->confirmingDelete = false;
        $this->guestToDelete = null;
    }

    /** 
     * Cancel delete action 
     */
    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->guestToDelete = null;
    }

    /** 
     * Render guest list with pagination and search 
     */
    public function render()
    {
        $user = Auth::user();

        // Check if user has customer relationship
        if (!$user || !$user->customer) {
            // Return empty paginated result to maintain compatibility with view
            $emptyGuests = Guest::with('customer')
                ->whereRaw('1=0') // This will return no results
                ->paginate(10);

            return view('livewire.guests.guest-list', [
                'guests' => $emptyGuests,
            ]);
        }

        $guests = Guest::with('customer')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->where('customer_id', $user->customer->id)
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        return view('livewire.guests.guest-list', [
            'guests' => $guests,
        ]);
    }
}
