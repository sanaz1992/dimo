<?php

namespace Modules\Instagram\Services;

use Modules\Core\Filters\QueryFilter;
use Modules\Core\Helpers\CodeGeneratorHelper;
use Modules\Instagram\Entities\InstagramAccount;
use Modules\Instagram\External\Repositories\Contract\InstagramAccountRepositoryInterface;

class InstagramAccountService
{
    public function __construct(
        protected InstagramAccountRepositoryInterface $instagramAccountRepository,
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->instagramAccountRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function findByColumn($col, $value)
    {
        return $this->instagramAccountRepository->findByColumn($col, $value);
    }

    public function updateOrCreate(array $condition, array $data)
    {
        $data['unique_code'] = CodeGeneratorHelper::generate(get_class(new InstagramAccount), 'unique_code');

        return $this->instagramAccountRepository->updateOrCreate(
            $condition,
            $data
        );
    }
}
