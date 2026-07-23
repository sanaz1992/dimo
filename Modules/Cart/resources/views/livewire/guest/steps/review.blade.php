<div class="panel cart-items mb-2">
    <div style="    min-height: 50px;">
        <h3 style="float: right">@lang('cart::attributes.shipping_address')</h3>
    </div>
    <label class="address-item" style="display: flex; cursor: pointer; width: 100%;">
        <div class="item-info">
            <h2>{{ $selectedAddress->receiver_name }} - {{ $selectedAddress->receiver_mobile }}</h2>
            <span>{{ $selectedAddress->city->name }} - {{ $selectedAddress->address }}</span>
        </div>
    </label>
</div>

@include('Cart::livewire.guest.steps.cart-items', ['canChange' => false])
