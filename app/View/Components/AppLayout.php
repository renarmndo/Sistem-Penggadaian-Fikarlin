<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public string $title;
    public string $role;

    /**
     * Create a new component instance.
     */
    public function __construct(string $title = '', string $role = 'petugas')
    {
        $this->title = $title;
        $this->role = $role;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
