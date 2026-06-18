<?php

namespace App\Livewire\Faq;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.main')]
class Faq extends Component
{
    public function render()
    {
        return view('livewire.faq.faq');
    }
}
