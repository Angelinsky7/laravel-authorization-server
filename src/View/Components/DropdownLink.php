<?php

namespace Darkink\AuthorizationServer\View\Components;

use Illuminate\View\Component;

class DropdownLink extends Component
{
    public function render()
    {
        return view('policy::components.dropdown-link');
    }
}
