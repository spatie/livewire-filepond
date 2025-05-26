<?php

namespace Spatie\LivewireFilepond;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\LivewireManager;
use Livewire\WithFileUploads;

trait WithFilePond
{
    use WithFileUploads;

    public function remove($property, $filename): void
    {
        $file = Str::after($filename, config('app.url'));
        $data = $this->getPropertyValue($property);
        app(LivewireManager::class)->updateProperty(
            $this,
            $property,
            is_array($this->getPropertyValue($property))
                ? array_values(array_filter($data, fn ($item) => $item !== $file))
                : null,
        );

        File::delete(public_path($file));
    }

    public function revert($property, $filename): void
    {
        if (! $this->hasProperty($property)) {
            return;
        }

        $uploads = $this->getPropertyValue($property);

        if (! is_array($uploads)) {
            if ($uploads instanceof TemporaryUploadedFile && $uploads->getFilename() === $filename) {
                $uploads->delete();
                app(LivewireManager::class)->updateProperty($this, $property, null);
            }

            return;
        }

        $newFiles = collect($uploads)
            ->filter(function ($upload) use ($filename) {
                if (! $upload instanceof TemporaryUploadedFile) {
                    return false;
                }

                if ($upload->getFilename() === $filename) {
                    $upload->delete();

                    return false;
                }

                return true;
            })->values()->toArray();

        app(LivewireManager::class)->updateProperty($this, $property, $newFiles);
    }

    /**
     * Default returns true, override in your component
     */
    public function validateUploadedFile(): bool
    {
        return true;
    }

    public function resetFilePond(string $property): void
    {
        $this->reset($property);
        $this->dispatch("filepond-reset-$property");
    }
}
