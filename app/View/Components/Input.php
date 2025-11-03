<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    /**
     * Determine if the input is disabled.
     *
     * @var bool
     */
    public $disabled;

    /**
     * The input type.
     *
     * @var string
     */
    public $type;

    /**
     * Create the component instance.
     *
     * @param  bool  $disabled
     * @param  string  $type
     * @return void
     */
    public function __construct($disabled = false, $type = 'text')
    {
        $this->disabled = $disabled;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return <<<'blade'
<input {{ $attributes->merge(['class' => 'border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm']) }}>
blade;
    }
}
