<?php

namespace Modules\Media\External;

use Modules\Media\Entities\Media;
use Modules\Media\External\Contracts\MediaRepositoryInterface;


class MediaRepository implements MediaRepositoryInterface
{
    public function create(array $data)
    {
        return Media::create([
            'mediaable_type' => $data['mediaable_type'],
            'mediaable_id' => $data['mediaable_id'],
            'collection'  => $data['collection'],
            'file_name'   => $data['file_name'],
            'thumbnail_path' => $data['thumbnail_path'],
            'origin_name' => $data['origin_name'],
            'mime_type'   => $data['mime_type'],
            'disk'        => $data['disk'],
        ]);
    }

    public function delete(Media $media)
    {
        $media->delete();
    }
}
