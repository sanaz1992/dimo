<?php

namespace Modules\Transactions\External;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Transactions\Entities\Transaction;
use Modules\Transactions\External\Contracts\TransactionRepositoryInterface;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }
}
