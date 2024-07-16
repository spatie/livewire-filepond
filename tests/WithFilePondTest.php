<?php

use Livewire\Livewire;
use Spatie\LivewireFilepond\Tests\TestSupport\Components\TestComponent;

it('can mount a Livewire component that has the filepond trait applied to it', function () {
    Livewire::test(TestComponent::class)->assertOk();
});
