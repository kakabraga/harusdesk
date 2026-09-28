<?php

namespace App\Actions\File;

use Illuminate\Support\Facades\Storage;

class DeleteFileAction
{
    public function execute(string $path, ?string $disk = null): bool
    {
        $disk = $disk ?? config('filesystems.default', 'local');

        if (! Storage::disk($disk)->exists($path)) {
            return false;
        }

        return Storage::disk($disk)->delete($path);
    }
}
