<div>
    <section class=" gap-4 anim-stagger">
        <x-Order::order.order-info :order="$order" :currency="$currency" />
    </section>

    <section class=" gap-4 anim-stagger">
        <livewire:order::products-table :order="$order" />
    </section>

</div>
