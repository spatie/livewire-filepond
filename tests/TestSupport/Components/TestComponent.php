<?php

namespace Spatie\LivewireFilepond\Tests\TestSupport\Components;

use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

class TestComponent extends Component
{
    use WithFilePond;

    public array $photos = [];

    public $photo = null;

    public function render()
    {
        return '<div>dummy</div>';
    }
}
