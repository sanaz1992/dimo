<?php

namespace Modules\Category\External;

use Modules\Category\Entities\Category;
use Modules\Category\External\Contracts\CategoryRepositoryInterface;
use Modules\Core\External\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
}
