<?php

namespace Darkink\AuthorizationServer\View\Components;

use Illuminate\View\Component;

class FormFieldError extends Component
{
    public string $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function render()
    {
        return view('policy::components.form-field-error');
    }

}
