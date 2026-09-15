<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Tag;
use Modules\Core\External\Repositories\Contract\TagRepositoryInterface;
use Modules\Core\Filters\QueryFilter;
use Modules\Core\Helpers\SlugHelper;
use Modules\Tenant\Services\TenantService;

class TagService
{
    public function __construct(
        protected TagRepositoryInterface $tagRepository
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->tagRepository->all($orderBy, $limit, $with, $conditions);
    }

    public function find($id)
    {
        return $this->tagRepository->find($id);
    }

    public function findByColumn($col, $value)
    {
        return $this->tagRepository->findByColumn($col, $value);
    }

    public function create(array $data): Tag
    {
        if (! isset($data['tenant_id']) && $data['tenant']) {
            $tenant = app(TenantService::class)->findByColumn('slug', $data['tenant']);
            if (! $tenant) {
                throw new \DomainException('Tenant not found.');
            } else {
                $data['tenant_id'] = $tenant->id;
            }
        }
        $data['slug'] = SlugHelper::generate(get_class(new Tag), $data['name']);

        return DB::transaction(function () use ($data) {
            return $this->tagRepository->create($data);
        });
    }

    public function update(Tag $tag, array $data): Tag
    {
        return DB::transaction(function () use ($tag, $data) {
            return $this->tagRepository->update($tag, $data);
        });
    }

    public function delete(Tag $tag): bool
    {
        return $this->tagRepository->delete($tag->id);
    }

    public function restore($slug)
    {
        return $this->tagRepository->restore($slug);
    }
}
