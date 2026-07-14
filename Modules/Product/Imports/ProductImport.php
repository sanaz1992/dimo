<?php

namespace Modules\Product\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Category\Entities\Category;
use Modules\Category\External\CategoryRepository;
use Modules\Core\Helpers\SlugHelper;
use Modules\Product\External\ProductRepository;
use Modules\Product\Services\ProductService;
use Modules\Warehouse\External\Repositories\RawMaterialWarehouseRepository;
use Modules\Warehouse\Services\UnitService;

class ProductImport implements ToCollection
{
    protected ProductRepository $productRepository;

    protected RawMaterialWarehouseRepository $rawMaterialWarehouseRepository;

    protected CategoryRepository $categoryRepository;

    protected ProductService $productService;

    public function __construct(
        ProductRepository $repo,
        RawMaterialWarehouseRepository $rawrepo,
        CategoryRepository $categoryRepo,
        ProductService $productService
    ) {
        $this->productRepository = $repo;
        $this->rawMaterialWarehouseRepository = $rawrepo;
        $this->categoryRepository = $categoryRepo;
        $this->productService = $productService;
    }

    /************ kiarad *************/
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            if ($key != 0) {
                if ($row[0]) {
                    $category = $this->categoryRepository->firstOrCreate(
                        ['title' => $row[2]],
                        ['slug' => SlugHelper::generate(get_class(new Category), $row[2])]
                    );
                    $product = $this->productRepository->firstOrCreate([
                        'code' => $row[0],
                    ], [
                        'title' => $row[1],
                        'category_id' => $category->id,
                        'price' => $row[3],
                        'has_fabric' => $row[4] == 'بله' ? true : false,
                        'is_publish' => true,
                    ]);

                }
                Log::info('product is:', ['product' => $product]);
                if ($row[5]) {
                    // $material = $this->rawMaterialWarehouseRepository->findByColumn('code', $row[5]);
                    $material = $this->rawMaterialWarehouseRepository->firstOrCreate([
                        'code' => $row[5],
                    ], [
                        'title' => $row[6],
                        //                        'unit' => 'number',
                        'unit_id' => resolve(UnitService::class)->findByColumn('value', 'number')->id,
                        'stock' => $row[9] ?? 100,
                        // 'price' => 1,
                        'warning_stock' => $row[10] ?? 20,
                        'minimum_stock' => $row[11] ?? 10,
                        'type' => $row[8] == 'wood' ? 'wood' : 'material',
                        'is_active' => $row[12] == 'فعال' ? true : false,
                    ]);
                    Log::info('material is:', ['material' => $material]);

                    $product->materials()->attach($material->id, ['qty' => $row[7]]);
                } else {
                    if ($row[6]) {
                        $this->productService->addFabric($product, [
                            'title' => $row[6],
                            'qty' => $row[7],
                        ]);
                    }
                }

                $product->load('hall_difinations');
                if (isset($row[13])) {
                    if ($row[13] == 'A') {
                        if (! $product->hall_difinations->where('hall_id', 3)->count()) {
                            $this->productRepository->createProductHallDefine($product, 3);
                        }
                    } elseif ($row[13] == 'B') {
                        if (! $product->hall_difinations->where('hall_id', 4)->count()) {
                            $this->productRepository->createProductHallDefine($product, 4);
                        }
                    }
                }
                if (isset($row[14])) {
                    if ($row[14] == 'A') {
                        if (! $product->hall_difinations->where('hall_id', 3)->count()) {
                            $this->productRepository->createProductHallDefine($product, 3);
                        }
                    } elseif ($row[14] == 'B') {
                        if (! $product->hall_difinations->where('hall_id', 4)->count()) {
                            $this->productRepository->createProductHallDefine($product, 4);
                        }
                    }
                }
            }
        }
    }

    // /************ venus *************/
    // public function collection(Collection $rows)
    // {
    //     foreach ($rows as $key => $row) {
    //         if ($key != 0) {
    //             if ($row[0]) {
    //                 $category = $this->categoryRepository->firstOrCreate(
    //                     ['slug' => SlugHelper::generate(get_class(new Product()), $row[2])],
    //                     ['title' => $row[2]]
    //                 );
    //                 $product = $this->productRepository->firstOrCreate([
    //                     'code' => $row[0]
    //                 ], [
    //                     'title' => $row[1],
    //                     'category_id' => $category->id,
    //                     'price' => $row[4],
    //                     'has_fabric' => $row[5] == "بله" ? true : false,
    //                     'is_publish' => true
    //                 ]);
    //                 Log::info('product is:', ['product' => $product]);
    //                 if ($product->has_fabric && $row[3]) {
    //                     $this->productService->addFabric($product, [
    //                         'title' => 'پارچه بدنه',
    //                         'qty' => $row[3]
    //                     ]);
    //                 }
    //                 $product->load('hall_difinations');
    //                 if ($row[6]) {
    //                     if ($row[6] == "A") {
    //                         if (!$product->hall_difinations->where('hall_id', 1)->count()) {
    //                             $this->productRepository->createProductHallDefine($product, 1);
    //                         }
    //                     } elseif ($row[6] == "B") {
    //                         if (!$product->hall_difinations->where('hall_id', 3)->count()) {
    //                             $this->productRepository->createProductHallDefine($product, 3);
    //                         }
    //                     } elseif ($row[6] == "C") {
    //                         if (!$product->hall_difinations->where('hall_id', 2)->count()) {
    //                             $this->productRepository->createProductHallDefine($product, 2);
    //                         }
    //                     }
    //                 }
    //                 if ($row[7]) {
    //                     if ($row[7] == "A") {
    //                         if (!$product->hall_difinations->where('hall_id', 1)->count()) {
    //                             $this->productRepository->createProductHallDefine($product, 1);
    //                         }
    //                     } elseif ($row[7] == "B") {
    //                         if (!$product->hall_difinations->where('hall_id', 3)->count()) {
    //                             $this->productRepository->createProductHallDefine($product, 3);
    //                         }
    //                     } elseif ($row[7] == "C") {
    //                         if (!$product->hall_difinations->where('hall_id', 2)->count()) {
    //                             $this->productRepository->createProductHallDefine($product, 2);
    //                         }
    //                     }
    //                 }
    //                 if ($row[8]) {
    //                     if ($row[8] == "A") {
    //                         if (!$product->hall_difinations->where('hall_id', 1)->count()) {
    //                             $this->productRepository->createProductHallDefine($product, 1);
    //                         }
    //                     } elseif ($row[8] == "B") {
    //                         if (!$product->hall_difinations->where('hall_id', 3)->count()) {
    //                             $this->productRepository->createProductHallDefine($product, 3);
    //                         }
    //                     } elseif ($row[8] == "C") {
    //                         if (!$product->hall_difinations->where('hall_id', 2)->count()) {
    //                             $this->productRepository->createProductHallDefine($product, 2);
    //                         }
    //                     }
    //                 }
    //             }
    //             if ($row[9]) {
    //                 $material = $this->rawMaterialWarehouseRepository->firstOrCreate([
    //                     'code' => $row[9]
    //                 ], [
    //                     'title' => $row[10],
    //                     'unit' => 'number',
    //                     'stock' => $row[12],
    //                     'price' => 1,
    //                     'warning_stock' => $row[13],
    //                     'minimum_stock' => $row[14],
    //                     'type' => $row[15] == "چوب" ? 'wood' : 'material',
    //                     'is_active' => $row[16] == "فعال" ? true : false
    //                 ]);
    //                 Log::info('materia is:', ['material' => $material]);

    //                 $product->materials()->attach($material->id, ['qty' => $row[11]]);
    //             }
    //             // $this->productService->publishProduct($product->id);
    //         }
    //     }
    // }
}
