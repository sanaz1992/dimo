<?php

namespace Modules\Product\Http\Livewire\Admin\Product;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Product\Imports\MarkzaProductImport;
use Modules\Product\Imports\ProductImport as ImportsProductImport;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductImport extends AdminBaseComponent
{
    use WithFileUploads;

    public $file;

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // مسیر فایل اکسل
        $filePath = $this->file->getRealPath();


        // مرحله ۱: استخراج تصاویر از اکسل
        $this->extractImages($filePath);

        $excelData = Excel::import(app(MarkzaProductImport::class), $this->file->getRealPath());
        Log::info('import data log');

        // Excel::import(new ProductImport(), $this->file->getRealPath());
        dd("اطلاعات با موفقیت ثبت شد");
        session()->flash('success', 'فایل با موفقیت ایمپورت شد');
    }

    private function extractImages($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $tempDir = storage_path('app/tmp_images');

        File::deleteDirectory($tempDir);
        mkdir($tempDir, 0777, true);

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        foreach ($worksheet->getDrawingCollection() as $drawing) {

            $path = $drawing->getPath();
            $mime = mime_content_type($path); // تشخیص واقعی نوع فایل
            // dd($path, $mime);
            // تبدیل MIME به پسوند
            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp'
            ];
            $ext = $extensions[$mime] ?? 'jpg'; // اگر نشناخت فرض بر jpg

            $coordinate = $drawing->getCoordinates();
            $row = preg_replace('/[A-Z]/', '', $coordinate);

            // ذخیره با پسوند درست
            copy($path, $tempDir . "/row_{$row}.{$ext}");
        }
    }

    public function render()
    {
        return $this->renderView(
            'Product::livewire.product.product-import',
        )->layoutData([
            'title' => __('product::attributes.product_list')
        ]);
    }
}
