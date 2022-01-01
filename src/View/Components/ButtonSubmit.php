<?php

namespace Darkink\AuthorizationServer\View\Components;

use Illuminate\View\Component;

class ButtonSubmit extends Component
{

    public string $color;

    public function __construct(string $color = 'primary')
    {
        $this->color = $color;
    }

    public function render()
    {
        return view('policy::components.button-submit');
    }
}
