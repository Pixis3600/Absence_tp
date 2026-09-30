<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputText extends Component
{
    public function __construct(
        public string $name,
        public string $label,
        public string $type = 'text',
        public mixed $value = null,
        public bool $required = false,
        public ?string $id = null,
    ) {
        $this->id = $this->id ?? $this->name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input-text');
    }
}
