<?php

namespace Modules\Core\External\Repositories;

use Modules\Core\Entities\Tag;
use Modules\Core\External\Repositories\Contract\TagRepositoryInterface;

class TagRepository extends BaseRepository implements TagRepositoryInterface
{
    public function __construct(Tag $model)
    {
        parent::__construct($model);
    }

    public function restore($code): Tag
    {
        $tag = Tag::withTrashed()->where('slug', $code)->first();
        if ($tag) {
            $tag->restore();
        }

        return $tag;
    }
}
