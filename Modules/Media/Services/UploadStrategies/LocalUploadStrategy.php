<?php

namespace Modules\Media\Services\UploadStrategies;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Modules\Media\Contracts\FileUploadStrategyInterface;

class LocalUploadStrategy implements FileUploadStrategyInterface
{
    public function upload(UploadedFile $file, string $path): string
    {
        return $file->store($path, 'public_html');
    }

    public function uploadMultiple(array $imagesBinary, string $dir, string $baseFilename, string $extension, string $disk)
    {
        $paths = [];

        foreach ($imagesBinary as $size => $content) {
            $filename = $size === 'original'
                ? "{$baseFilename}.{$extension}"
                : "{$baseFilename}-{$size}.{$extension}";

            $path = $dir . '/' . $filename;

            Storage::disk('public')->put($path, $content);

            $paths[$size] = $path;
        }

        return $paths;
    }

    public function delete(string $filePath)
    {
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}
