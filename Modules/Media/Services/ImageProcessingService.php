<?php

namespace Modules\Media\Services;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;


class ImageProcessingService
{
    /**
     * resize image
     *
     * @param UploadedFile $file
     * @param array $sizes [ 'thumb' => [100,100], 'medium' => [300,300] ]
     * @return array ['original' => binary, 'thumb' => binary, ...]
     */
    public function resizeAndPrepare(UploadedFile $file, array $sizes = []): array
    {
        $images = [];

        // main image
        $originalImage = ImageManager::imagick()->read($file->getRealPath());
        // Image::make($file->getRealPath());
        $images['original'] = (string) $originalImage->encode();

        // resize images
        foreach ($sizes as $sizeName => [$width, $height]) {
            $image = ImageManager::imagick()->read($file->getRealPath());
            $image->resize($width, $height);
            $images[$sizeName] = (string) $image->encode();
        }

        return $images;
    }
}
