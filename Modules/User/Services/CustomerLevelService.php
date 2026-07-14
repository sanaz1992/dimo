<?php

namespace Modules\User\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Exceptions\ApiException;
use Modules\Core\Filters\QueryFilter;
use Modules\Core\Helpers\SlugHelper;
use Modules\User\Entities\CustomerLevel;
use Modules\User\External\Repositories\Contract\CustomerLevelRepositoryInterface;

class CustomerLevelService
{
    public function __construct(
        protected CustomerLevelRepositoryInterface $customerLevelRepository
    ) {}

    public function list(string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], QueryFilter $filter = null)
    {
        return $this->customerLevelRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function findByColumn($col, $value)
    {
        return $this->customerLevelRepository->findByColumn($col, $value);
    }


    public function create(array $data): CustomerLevel
    {
        $data['slug'] = SlugHelper::generate(
            CustomerLevel::class,
            $data['title']
        );
        return DB::transaction(function () use ($data) {
            $customerLevel = $this->customerLevelRepository->create($data);
            return $customerLevel;
        });
    }

    public function update(CustomerLevel $customerLevel, array $data): CustomerLevel
    {
        return DB::transaction(function () use ($customerLevel, $data) {
            $customerLevel = $this->customerLevelRepository->update($customerLevel, $data);
            return $customerLevel;
        });
    }

    public function delete(CustomerLevel $customerLevel)
    {
        $customerLevel->load('leads');
        if ($customerLevel->leads->count()) {
            throw new ApiException(
                'به دلیل وجود لید در این وضعیت امکان حذف این وضعیت وجود ندارد.',
                500
            );
        }
        return $this->customerLevelRepository->delete($customerLevel->id);
    }
}
