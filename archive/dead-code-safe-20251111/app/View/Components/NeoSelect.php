<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * Neo Select Component - Context7 Standardı
 * Yalıhan Bekçi Onaylı Select Dropdown
 */
class NeoSelect extends Component
{
    public $name;
    public $label;
    public $required;
    public $options;
    public $value;
    public $placeholder;
    public $helpText;
    public $icon;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $name,
        $label,
        $options = [],
        $required = false,
        $value = '',
        $placeholder = 'Seçiniz',
        $helpText = null,
        $icon = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->required = $required;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->helpText = $helpText;
        $this->icon = $icon;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.neo-select');
    }
}
