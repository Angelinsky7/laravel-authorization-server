<?php

namespace Darkink\AuthorizationServer\View\Components;

use Illuminate\View\Component;

class Table  extends Component
{
    public function render()
    {
        return view('policy::components.table');
    }
}
