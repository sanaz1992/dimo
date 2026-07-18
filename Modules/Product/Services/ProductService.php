<?php

namespace Modules\Product\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Core\Filters\QueryFilter;
use Modules\Core\Traits\LivewireNotify;
use Modules\Media\Entities\Media;
use Modules\Media\Services\MediaService;
use Modules\Product\Entities\Product;
use Modules\Product\External\Contracts\ProductRepositoryInterface;

class ProductService
{
    use LivewireNotify;

    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected MediaService $mediaService
    ) {
    }

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->productRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function find($id)
    {
        return $this->productRepository->find($id);
    }

    public function create(array $data): Product
    {
        $image = $data['image'] ?? null;
        unset($data['image']);

        return DB::transaction(function () use ($data, $image) {
            $product = $this->productRepository->create($data);
            if ($image) {
                $dir = $product->uploadDir();
                $this->mediaService->upload($product, $image, 'main', $dir);
            }
            if (isset($data['gallery'])) {
                foreach ($data['gallery'] as $imageGallery) {
                    $this->mediaService->upload($product, $imageGallery, 'gallery', $dir);
                }
            }

            return $product;
        });
    }

    public function update(Product $product, array $data): Product
    {
        $image = $data['image'] ?? null;
        unset($data['image']);
        return DB::transaction(function () use ($product, $data, $image) {
            $product = $this->productRepository->update($product, $data);
            if ($image) {
                $oldImage = $product->main_image;
                $dir = $product->uploadDir();
                $this->mediaService->upload($product, $image, 'main', $dir);
                if ($oldImage  instanceof Media) {
                    $this->mediaService->delete($oldImage);
                }
            }
            return $product;
        });
    }
}
