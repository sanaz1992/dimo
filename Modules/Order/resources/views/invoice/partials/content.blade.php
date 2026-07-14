<div id="invoice-source" class="space-y-6" dir="rtl">
    <div id="order-info-block">
        <x-Order::order.order-info :order="$order" :currency="app(\Modules\Core\Helpers\SettingHelper::class)->currencyLabel()" />
    </div>

    <!-- Products Table -->
    @include('Order::invoice.partials.invoice-table')
</div>

{{-- Paginated output for multi-page printing --}}
<div id="invoice-pages" dir="rtl" style="display: none;"></div>
