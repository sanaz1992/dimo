<?php

namespace Modules\Category\External;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Category\Entities\Category;
use Modules\Category\External\Contracts\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
}
