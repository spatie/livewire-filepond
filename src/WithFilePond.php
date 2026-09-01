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

        $path = $this->normalizeFilePondPath(Str::after($filename, (string) config('app.url')));

        if (! $this->isSafeFilePondPath($path)) {
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

        $target = $this->filePondRemovalTarget($path);

        if ($target === null) {
            return;
        }

        if (! $this->canRemoveFile($path)) {
            return;
        }

        File::delete($target);
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

    /**
     * Default returns true, override in your component to narrow down
     * which files may be deleted from disk. The given path is relative
     * to the public directory and always starts with a slash.
     */
    protected function canRemoveFile(string $path): bool
    {
        return true;
    }

    /**
     * @return array<int, string>
     */
    protected function filePondRemovalRoots(): array
    {
        $links = config('filesystems.links') ?? [public_path('storage') => storage_path('app/public')];

        return collect([public_path(), ...array_keys($links)])
            ->map(fn (string $root) => realpath($root))
            ->filter()
            ->values()
            ->all();
    }

    protected function normalizeFilePondPath(string $path): string
    {
        $segments = array_filter(
            preg_split('#[/\\\\]#', $path) ?: [],
            fn (string $segment) => $segment !== '' && $segment !== '.',
        );

        return '/'.implode('/', $segments);
    }

    protected function isSafeFilePondPath(string $path): bool
    {
        if (str_contains($path, "\0")) {
            return false;
        }

        return ! in_array('..', preg_split('#[/\\\\]#', $path) ?: [], strict: true);
    }

    protected function filePondRemovalTarget(string $path): ?string
    {
        $target = realpath(public_path($path));

        if ($target === false) {
            return null;
        }

        foreach ($this->filePondRemovalRoots() as $root) {
            if (str_starts_with($target, $root.DIRECTORY_SEPARATOR)) {
                return $target;
            }
        }

        return null;
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
