<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        $role = Auth::user()?->role;

        return match ($role) {
            'admin' => view('layouts.admin'),
            'kurir' => view('layouts.kurir'),
            'siswa' => view('layouts.siswa'),
            default => view('layouts.siswa'),
        };
    }
}
