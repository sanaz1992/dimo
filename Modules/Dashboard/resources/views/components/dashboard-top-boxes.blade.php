<div class="bg-white md:bg-transparent grid grid-cols-2 sm:grid-cols-4 md:grid-cols-2 xl:grid-cols-4 md:gap-8 w-full rounded-xl divide-x divide-black/5 md:divide-x-0 rtl:divide-x-reverse divide-y sm:divide-y-0">
    <?php $firstTitle=$panel=="admin"?__('dashboard::attributes.awaiting_approved_orders'):__('dashboard::attributes.received_orders');
    $firstIcon=$panel=="admin"?'convert-3d-cube':'truck-tick';
    $lastIcon=$panel=="admin"?'truck-tick':'convert-3d-cube';
    $firstColor=$panel=="admin"?'bg-[#E5C60D]':'bg-[#20BF86]';
    $lastColor=$panel=="admin"?'bg-[#20BF86]':'bg-[#E5C60D]';
    ?>
    {{-- <x-dashboard::dashboard-top-box :title="$firstTitle"
        :order_count="$ordersReceivedCount" :icon="$firstIcon" :color="$firstColor"
        :route=" route($panel.'.orders.index',['activeTab'=>Modules\Order\Enums\OrderListTabs::PENDING->value])" />

    <x-dashboard::dashboard-top-box :title="__('dashboard::attributes.awaiting_production_orders')"
        :order_count="$ordersAwaitingProduction" icon="box-time" color="bg-[#4A8CE7]"
        :route=" route($panel.'.orders.index',['activeTab'=>Modules\Order\Enums\OrderListTabs::AWAITING_PRODUCTION->value])"/>

    <x-dashboard::dashboard-top-box :title="__('dashboard::attributes.in_production_orders')"
        :order_count="$ordersInProduction" icon="convert-3d-cube" color="bg-[#E5C60D]"
        :route=" route($panel.'.orders.index',['activeTab'=>Modules\Order\Enums\OrderListTabs::IN_PRODUCTION->value])" />

    <x-dashboard::dashboard-top-box :title="__('dashboard::attributes.produced_orders')" :order_count="$orderProduced"
        :icon="$lastIcon" :color="$lastColor"
        :route=" route($panel.'.orders.index',['activeTab'=>Modules\Order\Enums\OrderListTabs::PRODUCED->value])" /> --}}
</div>
