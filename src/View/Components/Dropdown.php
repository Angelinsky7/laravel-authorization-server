<?php

namespace Darkink\AuthorizationServer\View\Components;

use Illuminate\View\Component;

class Dropdown extends Component
{
    public function render()
    {
        return view('policy::components.dropdown');
    }
}
