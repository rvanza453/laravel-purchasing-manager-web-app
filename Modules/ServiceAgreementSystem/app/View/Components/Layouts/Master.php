<?php

namespace Modules\ServiceAgreementSystem\View\Components\Layouts;

use Illuminate\View\Component;
use Illuminate\View\View;

class Master extends Component
{
    public $title;

    public function __construct($title = null)
    {
        $this->title = $title ?? 'Service Agreement System';
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('serviceagreementsystem::components.layouts.master');
    }
}
