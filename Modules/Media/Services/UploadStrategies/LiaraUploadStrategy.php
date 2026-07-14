<?php

namespace Modules\Media\Services\UploadStrategies;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Modules\Media\Contracts\FileUploadStrategyInterface;


class LiaraUploadStrategy implements FileUploadStrategyInterface
{
    public function upload(UploadedFile $file, string $path): string
    {
        return $file->store($path, 's3');
    }

    public function uploadMultiple(array $imagesBinary, string $dir, string $baseFilename, string $extension, string $disk)
    {
        $paths = [];

        foreach ($imagesBinary as $size => $content) {
            $filename = $size === 'original'
                ? "{$baseFilename}.{$extension}"
                : "{$baseFilename}-{$size}.{$extension}";

            $path = $dir . '/' . $filename;
            Storage::disk($disk)->put($path, $content);

            $paths[$size] = $path;
        }

        return $paths;
    }


    public function delete(string $filePath)
    {
        if (Storage::disk('s3')->exists($filePath)) {
            Storage::disk('s3')->delete($filePath);
        }
    }
}
