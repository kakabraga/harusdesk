<?php

namespace App\Actions\File;

use App\DTOs\File\UploadedFileDTO;
use Illuminate\Http\UploadedFile;

class UploadFileAction
{
    public function execute(UploadedFile $file, string $directory, ?string $disk = null): UploadedFileDTO
    {
        $disk = $disk ?? config('filesystems.default', 'local');
        $path = $file->store($directory, $disk);

        if (! $path) {
            throw new \Exception('Failed to store uploaded file.');
        }

        return new UploadedFileDTO(
            originalName: $file->getClientOriginalName(),
            path: $path,
            mimeType: $file->getMimeType(),
            sizeKb: (int) ceil($file->getSize() / 1024)
        );
    }
}
