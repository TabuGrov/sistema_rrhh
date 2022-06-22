<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InputCheck extends Component
{
    public $id, $name, $label;
    public $topclass, $inputclass;
    public $checked, $disabled, $required;

    public function __construct(
            $id = 'checkbox', $name = null,
            $label = '',
            $topclass = null, $inputclass = null,
            $checked = false, $disabled = false, $required = false
        )
    {
        $this->id = $id;
        $this->name = $name;
        $this->label = $label;
        $this->topclass = $topclass;
        $this->inputclass = $inputclass;
        $this->checked = $checked;
        $this->required = $required;
        $this->disabled = $disabled;
    }

    public function render()
    {
        return view('components.input-check');
    }
}
