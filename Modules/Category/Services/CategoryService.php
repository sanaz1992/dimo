<?php

namespace Modules\Category\Services;

use Illuminate\Support\Facades\DB;
use Modules\Category\Entities\Category;
use Modules\Category\External\Contracts\CategoryRepositoryInterface;
use Modules\Core\Helpers\SlugHelper;
use Modules\Media\Entities\Media;
use Modules\Media\Services\MediaService;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
        protected MediaService $mediaService
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [])
    {
        return $this->categoryRepository->all($orderBy, $limit, $with, $conditions);
    }

    public function find(int $id): Category
    {
        return $this->categoryRepository->find($id);
    }

    public function create(array $data): Category
    {
        $image = $data['image'] ?? null;
        unset($data['image']);
        $data['slug'] = SlugHelper::generate(get_class(new Category), $data['name']);

        // $data['parent_id'] = strlen($data['parent_id']) ? $data['parent_id'] : null;
        return DB::transaction(function () use ($data, $image) {
            $category = $this->categoryRepository->create($data);
            if ($image) {
                $dir = $category->uploadDir();
                $this->mediaService->upload($category, $image, 'main', $dir);
            }

            return $category;
        });
    }

    public function update(Category $category, array $data): Category
    {
        $image = $data['image'] ?? null;
        unset($data['image']);

        return DB::transaction(function () use ($category, $data, $image) {
            $category = $this->categoryRepository->update($category, $data);
            if ($image) {
                $oldImage = $category->main_image;
                $dir = $category->uploadDir();
                $this->mediaService->upload($category, $image, 'main', $dir);
                if ($oldImage instanceof Media) {
                    $this->mediaService->delete($oldImage);
                }
            }

            return $category;
        });
    }

    public function deleteById(int $categoryId): bool
    {
        $category = $this->categoryRepository->find($categoryId);

        return $this->delete($category);
    }

    public function delete(Category $category): bool
    {
        return $this->categoryRepository->delete($category->id);
    }
}
