<?php

namespace Modules\Media\Contracts;

use Illuminate\Http\UploadedFile;

interface FileUploadStrategyInterface
{
    public function upload(UploadedFile $file, string $path): string;
    public function uploadMultiple(array $imagesBinary, string $dir, string $baseFilename, string $extension, string $disk);
    public function delete(string $filePath);
}
