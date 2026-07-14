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
            foreach ($data['gallery'] as $imageGallery) {
                $this->mediaService->upload($product, $imageGallery, 'gallery', $dir);
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

    public function addMaterial(Product $product, array $material)
    {
        $product->load('materials');
        if (count($product->materials->where('id', $material['id']))) {
            throw new InvalidArgumentException(__('product::messages.this_raw_material_has_already_been_added_to_the_product'));
        }
        $data[$material['id']] = [
            'qty' => $material['qty'],
        ];
        $this->productRepository->addMaterials($product, $data);
    }

    public function addIntermediateProductToMaterials(Product $product, array $material)
    {
        $product->load('intermediate_products');
        if (count($product->intermediate_products->where('id', $material['id']))) {
            throw new InvalidArgumentException(__('product::messages.this_raw_material_has_already_been_added_to_the_product'));
        }
        $data[$material['id']] = [
            'qty' => $material['qty'],
        ];
        $this->productRepository->addIntermediateProductMaterials($product, $data);
    }

    public function deleteMaterial(Product $product, int $materialId)
    {
        $this->productRepository->deleteMaterial($product, $materialId);
    }

    public function deleteIntermediateProductMaterial(Product $product, int $materialId)
    {
        $this->productRepository->deleteIntermediateProductMaterial($product, $materialId);
    }

    public function createProductHallDefine(Product $product, int $hallId, ?int $sort = null)
    {
        $product->load('hall_difinations');
        if ($product->hall_difinations->where('hall_id', $hallId)->count()) {
            throw new InvalidArgumentException(__('product::messages.this_stage_has_already_been_added_to_the_product'));
        }

        return $this->productRepository->createProductHallDefine($product, $hallId, $sort);
    }

    public function removeProductHallDefine(Product $product, int $hallId)
    {
        return $this->productRepository->removeProductHallDefine($product, $hallId);
    }

    public function updateCostItems(Product $product, array $data)
    {
        return DB::transaction(function () use ($product, $data) {
            $product = $this->productRepository->updateCostItems($product, $data);

            return $product;
        });
    }

    public function publishProduct(int $productId, ?bool $publish = null)
    {
        $product = $this->productRepository->find($productId);
        if ($this->checkProductCanPublished($product)) {
            $this->productRepository->publishProduct($product, $publish);

            return true;
        }
        throw new InvalidArgumentException(__('product::messages.this_product_need_more_details_for_publish'));
    }

    public function checkProductCanPublished(Product $product)
    {
        $hasMaterials = $product->materials()->exists() || $product->intermediate_products()->exists();

        if ($hasMaterials && $product->hall_difinations()->exists()) {
            if ($product->has_fabric) {
                return $product->required_fabrics()->exists();
            }

            return true;
        }

        return false;
    }

    public function addFabric(Product $product, array $data)
    {
        $this->productRepository->addFabric($product, $data);
    }

    public function deleteProductFabric(int $productFabricId)
    {
        $this->productRepository->deleteProductFabric($productFabricId);
    }
}
