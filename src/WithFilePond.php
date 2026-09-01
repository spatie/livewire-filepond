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

    public function remove(string $property, string $filename): void
    {
        if (! $this->hasProperty($property)) {
            return;
        }

        $path = Str::after($filename, (string) config('app.url'));

        if (! $this->isRemovableFilePondPath($path)) {
            return;
        }

        $uploads = $this->getPropertyValue($property);

        if (! $this->filePondPropertyHolds($uploads, $path, $filename)) {
            return;
        }

        app(LivewireManager::class)->updateProperty(
            $this,
            $property,
            is_array($uploads)
                ? array_values(array_filter($uploads, fn ($item) => $item !== $path && $item !== $filename))
                : null,
        );

        File::delete(public_path($path));
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

    protected function isRemovableFilePondPath(string $path): bool
    {
        if (str_contains($path, "\0")) {
            return false;
        }

        return ! in_array('..', preg_split('#[/\\\\]#', $path) ?: [], strict: true);
    }

    protected function filePondPropertyHolds(mixed $uploads, string $path, string $filename): bool
    {
        if (is_array($uploads)) {
            return in_array($path, $uploads, strict: true)
                || in_array($filename, $uploads, strict: true);
        }

        return $uploads === $path || $uploads === $filename;
    }
}
