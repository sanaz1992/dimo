<?php

namespace Modules\Instagram\Services;

use Modules\Core\Filters\QueryFilter;
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
}
