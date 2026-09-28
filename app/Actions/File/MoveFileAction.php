<?php

namespace App\Actions\File;

use Illuminate\Support\Facades\Storage;

class MoveFileAction
{
    public function execute(string $oldPath, string $newPath, ?string $disk = null): bool
    {
        $disk = $disk ?? config('filesystems.default', 'local');

        if (! Storage::disk($disk)->exists($oldPath)) {
            return false;
        }

        return Storage::disk($disk)->move($oldPath, $newPath);
    }
}
