<?php

namespace App\Livewire\PrivacyPolicy;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.main')]
class PrivacyPolicy extends Component
{
    public function render()
    {
        return view('livewire.privacy-policy.privacy-policy');
    }
}
