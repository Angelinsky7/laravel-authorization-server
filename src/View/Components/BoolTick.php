<?php

namespace Darkink\AuthorizationServer\View\Components;

use Illuminate\View\Component;

class BoolTick extends Component
{

    public bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function render()
    {
        return view('policy::components.booltick');
    }
}
