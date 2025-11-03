<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * Neo Input Component - Context7 Standardı
 * Yalıhan Bekçi Onaylı Form Input
 */
class NeoInput extends Component
{
    public $name;
    public $label;
    public $type;
    public $required;
    public $placeholder;
    public $value;
    public $helpText;
    public $icon;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $name,
        $label,
        $type = 'text',
        $required = false,
        $placeholder = '',
        $value = '',
        $helpText = null,
        $icon = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->required = $required;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->helpText = $helpText;
        $this->icon = $icon;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.neo-input');
    }
}
