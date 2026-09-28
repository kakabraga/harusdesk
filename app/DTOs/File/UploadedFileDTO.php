<?php

namespace App\DTOs\File;

class UploadedFileDTO
{
    public function __construct(
        public readonly string $originalName,
        public readonly string $path,
        public readonly string $mimeType,
        public readonly int $sizeKb
    ) {}
}
