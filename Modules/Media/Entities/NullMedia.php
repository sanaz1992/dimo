<?php

namespace Modules\Media\Entities;

class NullMedia
{
    public function getThumbnailUrl($size = null)
    {
        return asset("img/no-image.jpeg");
    }
}
