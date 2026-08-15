<?php

namespace Modules\Transactions\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Transactions\Entities\Transaction;
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

    public function update(Transaction $transaction, array $data)
    {
        return DB::transaction(function () use ($transaction, $data) {

            $transaction = $this->transactionRepository->update($transaction, $data);

            return $transaction;
        });
    }
}
