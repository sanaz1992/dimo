<?php

namespace Modules\Transactions\Services;

use Modules\Core\Filters\QueryFilter;
use Modules\Transactions\External\Contracts\TransactionRepositoryInterface;

class TransactionService
{
    public function __construct(
        protected TransactionRepositoryInterface $transactionRepository,
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->transactionRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function find($id)
    {
        return $this->transactionRepository->find($id);
    }

    public function findByColumn($col, $value)
    {
        return $this->transactionRepository->findByColumn($col, $value);
    }
}
