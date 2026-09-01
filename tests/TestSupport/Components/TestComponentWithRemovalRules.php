<?php

namespace Spatie\LivewireFilepond\Tests\TestSupport\Components;

use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

class TestComponentWithRemovalRules extends Component
{
    use WithFilePond;

    public array $photos = [];

    protected function canRemoveFile(string $path): bool
    {
        return str_starts_with($path, '/uploads/');
    }

    public function render()
    {
        return '<div>dummy</div>';
    }
}
