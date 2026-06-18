<?php

namespace App\Livewire\Terms;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.main')]
class Terms extends Component
{
    public function render()
    {
        return view('livewire.terms.terms');
    }
}
