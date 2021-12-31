<?php

namespace Darkink\AuthorizationServer\View\Components;

use Illuminate\View\Component;

abstract class Button extends Component
{

    public string $color;
    public string $caption;
    public string $type;

    public function __construct(string $color, string $caption, string $type = 'submit')
    {
        $this->color = $color;
        $this->caption = $caption;
        $this->type = $type;
    }

}
