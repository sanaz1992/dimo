<?php

namespace Modules\Product\External;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\External\Repositories\BaseRepository;
use Modules\Core\Helpers\SlugHelper;
use Modules\Factory\Entities\ProductHallDifination;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductRequiredFabric;
use Modules\Product\External\Contracts\ProductRepositoryInterface;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): Product
    {
        // 'title', 'slug', 'code', 'category_id', 'description', 'price', 'is_active'
        $data['slug'] = SlugHelper::generate(get_class(new Product), $data['title']);
        $data['description'] = $data['description'] ?? null;

        return Product::create($data);
    }

    public function firstOrCreate(array $condition, array $data): Product
    {
        $data['slug'] = SlugHelper::generate(get_class(new Product), $data['title']);

        return Product::firstOrCreate(
            $condition,   // شرط جستجو
            $data
        );
    }

    public function update(Model $product, array $data): ?Model
    {
        $product->update([
            'title' => $data['title'],
            'code' => $data['code'],
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'description' => $data['description'] ?? null,
            'has_fabric' => $data['has_fabric'],
            'has_frame' => $data['has_frame'],
            'is_publish' => isset($data['is_publish']) ? $data['is_publish'] : $product->is_publish,
        ]);

        return $product;
    }

    public function addMaterials(Product $product, array $materials): void
    {
        $product->materials()->attach($materials);
    }

    public function deleteMaterial(Product $product, int $materialId): void
    {
        $product->materials()->detach($materialId);
    }

    public function addIntermediateProductMaterials(Product $product, array $data): void
    {
        $product->intermediate_products()->attach($data);
    }

    public function deleteIntermediateProductMaterial(Product $product, int $materialId): void
    {
        $product->intermediate_products()->detach($materialId);
    }

    public function createProductHallDefine(Product $product, int $hallId, ?int $sort = null): void
    {
        if (! $sort) {
            $sort = $product->hall_difinations()->max('sort') + 1;
        }
        $product->hall_difinations()->create([
            'hall_id' => $hallId,
            'sort' => $sort,
        ]);
    }

    public function removeProductHallDefine(Product $product, int $hallId): void
    {
        ProductHallDifination::where([['hall_id', $hallId], ['product_id', $product->id]])->delete();

        // //update sort
        // $remainingStages = ProductHallDifination::where('product_id', $product->id)
        //     ->orderBy('sort')
        //     ->get();
        // foreach ($remainingStages as $index => $stage) {
        //     $stage->sort = $index + 1;
        //     $stage->save();
        // }
    }

    public function publishProduct(Product $product, ?bool $publish = null): Product
    {
        if ($publish) {
            $product->is_publish = $publish;
        } else {
            $product->is_publish = ! $product->is_publish;
        }
        $product->save();

        return $product;
    }

    public function addFabric(Product $product, array $data)
    {
        ProductRequiredFabric::create([
            'product_id' => $product->id,
            'title' => $data['title'],
            'qty' => $data['qty'],
        ]);
    }

    public function deleteProductFabric(int $productFabricId)
    {
        ProductRequiredFabric::destroy($productFabricId);
    }

    public function updateCostItems(Product $product, array $data): Product
    {
        $costArray = [];
        foreach ($data as $id => $amount) {
            $costArray[$id] = [
                'amount' => $amount,
            ];
        }

        $product->cost_items()->sync($costArray);

        return $product;
    }
}
