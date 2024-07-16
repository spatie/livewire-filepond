<?php

namespace Spatie\LivewireFilepond\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LivewireFilepond\LivewireFilepondServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            LivewireFilepondServiceProvider::class,
        ];
    }


}
