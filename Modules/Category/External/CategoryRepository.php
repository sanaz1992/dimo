<?php

namespace Modules\Category\External;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\External\Repositories\BaseRepository;
use Modules\Core\Helpers\SlugHelper;
use Modules\Category\Entities\Category;
use Modules\Category\External\Contracts\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): Model
    {
        return Category::create([
            'name' => $data['name'],
            'slug'  => SlugHelper::generate(get_class(new Category()), $data['name']),
            'parent_id' => $data['parent_id'] ?? null,
            'type' => $data['type']
        ]);
    }

    public function update(Model $category, array $data): ?Model
    {
        $category->update([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
            'type' => $data['type'],
        ]);
        return $category;
    }
}
