<?php

namespace Modules\Product\External;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\External\Repositories\BaseRepository;
use Modules\Core\Helpers\CodeGeneratorHelper;
use Modules\Core\Helpers\SlugHelper;
use Modules\Product\Entities\Product;
use Modules\Product\External\Contracts\ProductRepositoryInterface;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): Product
    {
        $data['slug'] = SlugHelper::generate(get_class(new Product()), $data['name']);
        $data['description'] = $data['description'] ?? null;
        $data['code'] = $data['code'] ?? CodeGeneratorHelper::generate(get_class(new Product()));

        return Product::create($data);
    }

    public function firstOrCreate(array $condition, array $data): Product
    {
        $data['slug'] = SlugHelper::generate(get_class(new Product()), $data['name']);

        return Product::firstOrCreate(
            $condition,   // شرط جستجو
            $data
        );
    }

    public function update(Model $product, array $data): ?Model
    {
        $product->update([
            'name' => $data['name'],
            'code' => $data['code'] ?? $product->code,
            'category_id' => $data['category_id'],
            'grade' => $data['grade'],
            'extraction_method' => $data['extraction_method'],
            'description' => $data['description'] ?? null,
            'is_active' => isset($data['is_active']) ? $data['is_active'] : $product->is_active,
        ]);

        return $product;
    }

}
