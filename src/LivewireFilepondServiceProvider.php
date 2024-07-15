<?php

namespace Spatie\LivewireFilepond;

use Composer\InstalledVersions;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Livewire\Drawer\Utils;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewireFilepondServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-filepond')
            ->hasAssets()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        Route::get('_filepond/scripts', function () {
            return Utils::pretendResponseIsFile(__DIR__.'/../resources/dist/filepond.js');
        })->name('livewire-filepond.scripts');

        Blade::component('livewire-filepond::upload', 'filepond::upload');

        Blade::directive('filepondScripts', function () {
            $version = InstalledVersions::getPrettyVersion('spatie/livewire-filepond');

            $scripts = [];

            // Default to dynamic js (served by a Laravel route).
            $fullAssetPath = route('livewire-filepond.scripts');

            // Use static assets if they have been published
            if (file_exists(public_path('vendor/livewire-filepond/filepond.js'))) {
                $fullAssetPath = asset('/vendor/livewire-filepond/filepond.js');
            }

            if (is_file(__DIR__.'/../resources/hot')) {
                $url = rtrim(file_get_contents(__DIR__.'/../resources/hot'));

                $scripts[] = sprintf('<script type="module" src="%s" defer data-navigate-track></script>', "{$url}/resources/js/filepond.js");
                $scripts[] = sprintf('<script type="module" src="%s" defer data-navigate-track></script>', "{$url}/@vite/client");
            } else {
                $scripts[] = <<<HTML
                    <script type="module" src="{$fullAssetPath}?v={$version}" data-navigate-once defer data-navigate-track></script>
                HTML;
            }

            return implode("\n", $scripts);
        });
    }
}
