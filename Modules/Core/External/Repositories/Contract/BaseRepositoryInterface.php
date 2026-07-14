<?php

namespace Modules\Core\External\Repositories\Contract;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filters\QueryFilter;

interface BaseRepositoryInterface
{
    public function all(
        string $orderBy = null,
        array $limit = [],
        array $with = [],
        array $conditions = [],
        QueryFilter $filter = null
    );
    public function find(int $id): ?Model;
    public function findByColumn(string $col, string $val, bool $withTrashed = false): ?Model;
    public function firstOrCreate(array $condition, array $data): Model;
    public function create(array $data): Model;
    public function update(Model $record, array $data): ?Model;
    public function delete(int $id);
}
