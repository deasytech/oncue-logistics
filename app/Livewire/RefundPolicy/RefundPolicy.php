<?php

namespace App\Livewire\RefundPolicy;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.main')]
class RefundPolicy extends Component
{
    public function render()
    {
        return view('livewire.refund-policy.refund-policy');
    }
}
