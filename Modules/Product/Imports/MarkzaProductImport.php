<?php

namespace Modules\Product\Imports;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Category\Entities\Category;
use Modules\Category\External\CategoryRepository;
use Modules\Core\Helpers\SlugHelper;
use Modules\Media\Services\MediaService;
use Modules\Product\Enums\ProductOrderType;
use Modules\Product\External\ProductRepository;
use Modules\Product\Services\ProductService;
use Modules\Warehouse\External\Repositories\RawMaterialWarehouseRepository;

class MarkzaProductImport implements ToCollection
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
                // dd(is_int($row[7]));
                // dd(
                //     '0' . $row[0],
                //     '1' . $row[1],
                //     '2' . $row[2],
                //     '3' . $row[3],
                //     '4' . $row[4],
                //     '5' . $row[5],
                //     '6' . $row[6],
                //     '7' . $row[7],
                //     '8' . $row[8],
                //     // '9' . $row[9],
                //     // '10' . $row[10],
                //     // '11' . $row[11],
                //     // '12' . $row[12],
                //     // '13' . $row[13],
                // );
                if ($row[0]) {
                    $category = $this->categoryRepository->firstOrCreate(
                        ['title' => $row[0]],
                        ['slug' => SlugHelper::generate(get_class(new Category), $row[0])]
                    );
                }
                // dd($row[6], $row[7]);
                if ($row[7] && $row[7] != '_' && is_int($row[7])) {
                    $product = $this->productRepository->firstOrCreate([
                        'code' => $row[1],
                    ], [
                        'title' => $row[2],
                        'category_id' => $category->id,
                        'price' => $row[7],
                        'has_fabric' => true,
                        'is_publish' => true,
                        'order_type' => ProductOrderType::LEATHER->value,
                    ]);
                    Log::info('product is:', ['product' => $product]);
                    $this->importMedia($key, $product);
                }
                if ($row[8] && $row[8] != '_' && is_int($row[8])) {
                    $product = $this->productRepository->firstOrCreate([
                        'code' => $row[1].'-z',
                    ], [
                        'title' => $row[2],
                        'category_id' => $category->id,
                        'price' => $row[8],
                        'has_fabric' => true,
                        'is_publish' => true,
                        'order_type' => ProductOrderType::BASE->value,
                    ]);
                    Log::info('product is:', ['product' => $product]);
                    $this->importMedia($key, $product);
                }
            }
        }
    }

    public function importMedia($key, $product)
    {
        $rowNumber = $key + 1;

        // پیدا کردن فایلی که با row_X شروع می‌شود (با هر پسوندی)
        $files = glob(storage_path("app/tmp_images/row_{$rowNumber}.*"));

        if (! empty($files)) {
            $tempImage = $files[0]; // اولین فایل پیدا شده

            $dir = $product->uploadDir();

            // ساخت فایل برای سرویس
            $file = new UploadedFile(
                $tempImage,
                basename($tempImage),
                mime_content_type($tempImage),
                null,
                true
            );

            // آپلود
            resolve(MediaService::class)->upload($product, $file, 'main', $dir);

            // پاک کردن فایل موقت بعد از استفاده
            // unlink($tempImage);
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
