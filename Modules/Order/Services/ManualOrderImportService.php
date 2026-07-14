<?php

namespace Modules\Order\Services;

use Illuminate\Support\Facades\DB;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderItem;
use Modules\Order\Enums\OrderItemStatus;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\OrderType;
use Modules\Product\Entities\Product;
use Modules\User\Entities\User;
use Modules\User\Enums\UserLevel;
use Modules\Warehouse\Entities\Color;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Imports historical invoices (e.g. Mr. Monesi's order list) as MANUAL orders.
 *
 * MANUAL orders are decoupled from the production pipeline: prices are entered
 * by hand (left null = "pending pricing"), fabric consumption is not auto
 * calculated, and missing products are auto-created as unpublished stubs.
 */
class ManualOrderImportService
{
    /** Seller who owns imported orders (admin: sanaz / 09358364707). */
    protected int $sellerId;

    /** Excel column letters (data starts at row 4, header at row 3). */
    protected const COL_INVOICE = 'A';

    protected const COL_DATE = 'B';

    protected const COL_NAME = 'C';

    protected const COL_QTY = 'D';

    protected const COL_UNIT = 'E';

    protected const COL_CODE = 'F';

    protected const COL_CUSTOMER = 'G';

    protected const COL_PERSON = 'H';

    protected const COL_COLOR = 'I';

    protected const COL_NOTE1 = 'J';

    protected const COL_NOTE2 = 'K';

    protected const FIRST_DATA_ROW = 4;

    /** Accounting adjustment line, not a real product. */
    protected const SKIP_CODES = ['10700001'];

    protected const UNIT_FABRIC = 'متر';

    /** Cache: frame color title (normalized) => color id. */
    protected array $frameColorMap = [];

    /** Cache: seller (نماینده) normalized name => user id. */
    protected array $sellerMap = [];

    /** Report accumulators. */
    protected array $report;

    public function __construct(int $sellerId)
    {
        $this->sellerId = $sellerId;
    }

    public function import(string $path, bool $dryRun = true, bool $fresh = false): array
    {
        $this->report = [
            'dry_run' => $dryRun,
            'orders_created' => 0,
            'items_created' => 0,
            'fabric_items_created' => 0,
            'stub_products_created' => [],
            'deleted_orders' => 0,
            'skipped_rows' => 0,
            'skipped_orders' => [],   // invoices skipped entirely + reason
            'unmapped_colors' => [],  // color text with no frame-color match
            'duplicate_orders' => [], // invoice codes that already exist
            'matched_sellers' => [],  // "person text => seller name" that matched a نماینده
            'unmatched_persons' => [], // column H text with no seller match (fell back to admin)
        ];

        $this->loadFrameColors();
        $this->loadSellers();

        $invoices = $this->readInvoices($path);

        DB::beginTransaction();
        try {
            if ($fresh) {
                $this->purgeExistingManualOrders();
            }

            foreach ($invoices as $code => $rows) {
                $this->importInvoice((string) $code, $rows);
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->report;
    }

    /** Group non-empty data rows by invoice number (column A). */
    protected function readInvoices(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();

        $invoices = [];
        for ($row = self::FIRST_DATA_ROW; $row <= $highestRow; $row++) {
            $invoice = $this->cell($sheet, self::COL_INVOICE, $row);
            if ($invoice === '') {
                continue;
            }

            $invoices[$invoice][] = [
                'name' => $this->cell($sheet, self::COL_NAME, $row),
                'qty' => $this->cell($sheet, self::COL_QTY, $row),
                'unit' => $this->cell($sheet, self::COL_UNIT, $row),
                'code' => $this->cell($sheet, self::COL_CODE, $row),
                'customer' => $this->cell($sheet, self::COL_CUSTOMER, $row),
                'person' => $this->cell($sheet, self::COL_PERSON, $row),
                'color' => $this->cell($sheet, self::COL_COLOR, $row),
                'note' => trim($this->cell($sheet, self::COL_NOTE1, $row).' '.$this->cell($sheet, self::COL_NOTE2, $row)),
            ];
        }

        return $invoices;
    }

    protected function importInvoice(string $code, array $rows): void
    {
        if (Order::where('code', $code)->exists()) {
            $this->report['duplicate_orders'][] = $code;
            $this->report['skipped_orders'][] = "$code — قبلاً ثبت شده";

            return;
        }

        // Split product rows from fabric rows, dropping accounting lines.
        // Both become order items; fabrics keep their decimal meter qty.
        $productRows = [];
        $fabricRows = [];
        foreach ($rows as $r) {
            if (in_array($r['code'], self::SKIP_CODES, true)) {
                $this->report['skipped_rows']++;

                continue;
            }
            if ($r['unit'] === self::UNIT_FABRIC || str_contains($r['name'], 'پارچه')) {
                $fabricRows[] = $r;

                continue;
            }
            if ($r['code'] === '' && $r['name'] === '') {
                $this->report['skipped_rows']++;

                continue;
            }
            $productRows[] = $r;
        }

        if (empty($productRows) && empty($fabricRows)) {
            $this->report['skipped_orders'][] = "$code — بدون آیتم محصول";

            return;
        }

        // Decision: a missing PRODUCT no longer blocks the invoice — we
        // auto-create an unpublished stub. So every invoice is importable.
        $meta = $productRows[0] ?? $fabricRows[0];
        $description = $this->buildDescription($meta);
        $sellerId = $this->resolveSeller($meta['person']);

        $order = Order::create([
            'code' => $code,
            'seller_id' => $sellerId,
            'user_id' => null,
            'status' => OrderStatus::DRAFT->value,
            'type' => OrderType::MANUAL->value,
            'subtotal' => 0,
            'total_price' => 0,
            'description' => $description,
        ]);

        foreach ($productRows as $r) {
            $this->createOrderItem($order, $r, (int) ($r['qty'] ?: 1));
            $this->report['items_created']++;
        }

        // Each fabric line becomes its own order item (qty = meters).
        foreach ($fabricRows as $r) {
            $this->createOrderItem($order, $r, (float) ($r['qty'] ?: 1));
            $this->report['fabric_items_created']++;
        }

        $this->report['orders_created']++;
    }

    protected function createOrderItem(Order $order, array $r, int|float $qty): void
    {
        $product = $this->resolveProduct($r);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => $qty,
            'price' => null, // pending pricing
            'total_price' => null,
            'status' => OrderItemStatus::DRAFT->value,
            'frame_color_id' => $this->resolveFrameColor($r['color']),
            'custom_frame' => false,
            'send_fabric_by_customer' => false,
        ]);
    }

    /** Delete previously imported MANUAL orders (and their items) for a clean re-import. */
    protected function purgeExistingManualOrders(): void
    {
        $orderIds = Order::where('type', OrderType::MANUAL->value)
            ->where('seller_id', $this->sellerId)
            ->pluck('id');

        if ($orderIds->isEmpty()) {
            return;
        }

        OrderItem::whereIn('order_id', $orderIds)->delete();
        $this->report['deleted_orders'] = Order::whereIn('id', $orderIds)->delete();
    }

    protected function buildDescription(array $meta): string
    {
        $parts = [];
        if ($meta['person'] !== '') {
            $parts[] = 'نماینده: '.$meta['person'];
        }
        if ($meta['customer'] !== '') {
            $parts[] = 'مشتری: '.$meta['customer'];
        }

        return implode("\n", $parts);
    }

    /** Find product by code, or create an unpublished stub. */
    protected function resolveProduct(array $r): Product
    {
        $product = Product::where('code', $r['code'])->first();
        if ($product) {
            return $product;
        }

        $product = Product::create([
            'code' => $r['code'] ?: null,
            'title' => $r['name'] ?: ('کالای '.$r['code']),
            'slug' => $this->uniqueSlug($r['name'], $r['code']),
            'category_id' => $this->guessCategoryId($r['name']),
            'price' => 0,
            'is_publish' => false,
            'has_fabric' => false,
            'has_frame' => false,
        ]);

        $this->report['stub_products_created'][] = $r['code'].' | '.$r['name'];

        return $product;
    }

    protected function uniqueSlug(string $name, string $code): string
    {
        $base = trim(preg_replace('/\s+/u', '-', $name)) ?: 'product';

        return $base.'-'.($code ?: uniqid());
    }

    /** Best-effort category from product name; falls back to مبلمان (2). */
    protected function guessCategoryId(string $name): int
    {
        $map = [
            'کوسن' => 6,
            'فریم صندلی' => 7,
            'صندلی' => 7,
            'میز غذاخوری' => 1,
            'میز غذا خوری' => 1,
            'میز تلویزیون' => 3,
            'tv' => 3,
            'کنسول' => 5,
            'میز جلو مبلی' => 4,
            'عسلی' => 4,
            'مبل' => 2,
        ];

        foreach ($map as $needle => $id) {
            if (mb_stripos($name, $needle) !== false) {
                return $id;
            }
        }

        return 2;
    }

    protected function loadSellers(): void
    {
        User::where('level', UserLevel::SELLER->value)
            ->get(['id', 'name'])
            ->each(function ($seller) {
                $norm = $this->normalize((string) $seller->name);
                if ($norm !== '') {
                    $this->sellerMap[$norm] = $seller->id;
                }
            });
    }

    /**
     * Best-effort match of the excel "اشخاص" text (column H) to a seller (نماینده).
     *
     * The excel value often carries extra words (e.g. "گالری مبل کنزو - جناب آقای
     * حمید شریعت"), so we match a seller whose name appears as a substring of the
     * person text (space-insensitive). Falls back to the admin seller when there
     * is no confident match — the raw text is still kept in the order description.
     */
    protected function resolveSeller(string $person): int
    {
        $person = trim($person);
        if ($person === '') {
            return $this->sellerId;
        }

        $normPerson = str_replace(' ', '', $this->normalize($person));

        foreach ($this->sellerMap as $name => $id) {
            $normName = str_replace(' ', '', $name);
            if ($normName !== '' && str_contains($normPerson, $normName)) {
                $this->report['matched_sellers'][] = $person.' => '.$name;

                return $id;
            }
        }

        if (! in_array($person, $this->report['unmatched_persons'], true)) {
            $this->report['unmatched_persons'][] = $person;
        }

        return $this->sellerId;
    }

    protected function loadFrameColors(): void
    {
        Color::where('is_frame_color', true)->get()->each(function ($color) {
            $this->frameColorMap[$this->normalize($color->title)] = $color->id;
        });
    }

    /** Synonyms applied to the excel value before matching frame colors. */
    protected const COLOR_SYNONYMS = [
        'طوسی' => 'خاکستری', // grey
    ];

    protected function resolveFrameColor(string $value): ?int
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $norm = $this->normalize($value);
        foreach (self::COLOR_SYNONYMS as $from => $to) {
            $norm = str_replace($from, $to, $norm);
        }

        // Exact normalized match.
        if (isset($this->frameColorMap[$norm])) {
            return $this->frameColorMap[$norm];
        }

        // Substring match either direction, space-insensitive (handles
        // "خودرنگ" vs "خود رنگ" and values carrying trailing notes).
        $normNoSpace = str_replace(' ', '', $norm);
        foreach ($this->frameColorMap as $title => $id) {
            if ($title === '') {
                continue;
            }
            $titleNoSpace = str_replace(' ', '', $title);
            if (str_contains($normNoSpace, $titleNoSpace) || str_contains($titleNoSpace, $normNoSpace)) {
                return $id;
            }
        }

        if (! in_array($value, $this->report['unmapped_colors'], true)) {
            $this->report['unmapped_colors'][] = $value;
        }

        return null;
    }

    protected function normalize(string $value): string
    {
        // Collapse whitespace, drop ZWNJ, unify Arabic/Persian ي/ك.
        $value = str_replace(["\u{200c}", 'ي', 'ك'], [' ', 'ی', 'ک'], $value);

        return trim(preg_replace('/\s+/u', ' ', $value));
    }

    protected function cell($sheet, string $col, int $row): string
    {
        $value = $sheet->getCell($col.$row)->getValue();

        return $value === null ? '' : trim((string) $value);
    }
}
