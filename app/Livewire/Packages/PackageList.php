<?php

namespace App\Livewire\Packages;

use App\Models\Package;
use Livewire\Component;
use Livewire\WithPagination;

class PackageList extends Component
{
    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'tailwind';

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function customize($packageId)
    {
        return redirect()->route('packages.customize', ['packageId' => $packageId]);
    }

    public function render()
    {
        $packages = Package::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->paginate(9);

        return view('livewire.packages.package-list', [
            'packages' => $packages,
        ]);
    }
}
