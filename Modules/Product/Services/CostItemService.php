<?php

namespace Modules\Product\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Core\Helpers\SlugHelper;
use Modules\Product\Entities\CostItem;
use Modules\Product\External\Contracts\CostItemRepositoryInterface;

class CostItemService
{
    public function __construct(
        protected CostItemRepositoryInterface $costItemRepository,
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [])
    {
        return $this->costItemRepository->all($orderBy, $limit, $with, $conditions);
    }

    public function create(array $data): CostItem
    {
        $data['slug'] = SlugHelper::generate(get_class(new CostItem), $data['title'], 'slug');

        return DB::transaction(function () use ($data) {
            $costItem = $this->costItemRepository->create($data);

            return $costItem;
        });
    }

    public function find(int $id)
    {
        return $this->costItemRepository->find($id);
    }

    public function update(Model $costItem, array $data): ?Model
    {
        $data['slug'] = SlugHelper::generate(get_class(new CostItem), $data['title'], 'slug');

        return DB::transaction(function () use ($costItem, $data) {
            $costItem = $this->costItemRepository->update($costItem, $data);

            return $costItem;
        });
    }

    public function delete(int $id)
    {
        $costItem = $this->costItemRepository->find($id);
        $costItem->load('products');
        if (count($costItem->products)) {
            throw new Exception('برای مدل مورد نظر پارچه ثبت شده است و امکان حذف وجود ندارد');
        } else {
            $this->costItemRepository->delete($id);
        }
    }
}
