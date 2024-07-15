<?php

namespace Spatie\LivewireFilepond;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

trait WithFilePond
{
    use WithFileUploads;

    public function remove($property, $filename): void
    {
        $file = Str::after($filename, config('app.url'));

        File::delete(public_path($file));
    }

    public function revert($property, $filename): void
    {
        if (! $this->$property) {
            return;
        }

        if (! is_array($this->$property)) {
            $this->$property->delete();

            return;
        }

        $this->$property = array_filter($this->$property, function (TemporaryUploadedFile $file) use ($filename) {
            if ($file->getFilename() !== $filename) {
                return true;
            }

            $file->delete();

            return false;
        });
    }
}
