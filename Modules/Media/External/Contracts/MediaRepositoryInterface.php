<?php

namespace Modules\Media\External\Contracts;

use Modules\Media\Entities\Media;

interface MediaRepositoryInterface
{
    public function create(array $data);

    public function delete(Media $media);
}
