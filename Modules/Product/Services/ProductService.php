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
use Modules\Product\External\Contracts\ProductSkuRepositoryInterface;

class ProductService
{
    use LivewireNotify;

    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected MediaService $mediaService,
        protected ProductSkuRepositoryInterface $productSkuRepository
    ) {}

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

    public function createProductSku(Product $product, array $skuData)
    {
        $skuData['product_id'] = $product->id;

        return DB::transaction(function () use ($skuData) {
            $productSku = $this->productSkuRepository->create($skuData);

            return $productSku;
        });
    }

    public function removeProductSku(Product $product, int $skuId)
    {
        $product->loadCount(['skus' => function ($q) use ($skuId) {
            $q->where('id', $skuId);
        }]);
        if (! $product->skus_count) {
            throw new InvalidArgumentException(__('product::messages.the_selected_variation_is_not_available_for_this_product'));
        }

        return DB::transaction(function () use ($skuId) {
            $result = $this->productSkuRepository->delete($skuId);

            return $result;
        });
    }
}
