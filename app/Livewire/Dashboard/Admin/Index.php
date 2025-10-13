<?php

namespace App\Livewire\Dashboard\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]  // Au lieu de 'components.layouts.app'
#[Title('Dashboard Admin')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.dashboard.admin.index');
    }
}
