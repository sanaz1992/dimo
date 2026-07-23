<!-- Cart Items -->
<div class="panel cart-items ">
    <div style="    min-height: 50px;">
        <h3 style="float: right">@lang('cart::attributes.select_or_create_address')</h3>
        <button type="submit" class="btn-submit-comment btn-submit-light" style="float: left"
            wire:click="changeAddressFormStatus">@lang('cart::attributes.create_address')</button>
    </div>
    @foreach ($user->addresses as $address)
        <label for="address-{{ $address->id }}"
            class="address-item {{ $selectedAddressId == $address->id ? 'active' : '' }}"
            style="display: flex; cursor: pointer; width: 100%;">

            <input type="radio" id="address-{{ $address->id }}" value="{{ $address->id }}"
                wire:model.live="selectedAddressId" class="remove-item hidden" />

            <div class="item-info">
                <h2>{{ $address->receiver_name }} - {{ $address->receiver_mobile }}</h2>
                <span>{{ $address->city->name }} - {{ $address->address }}</span>
            </div>
        </label>
    @endforeach

    @if($showAddressForm)
        <div class="comments-layout">
            <form wire:submit.prevent="submitAddress" class="comment-form">
                <div class="comment-form-grid">
                    <div class="comment-field-group">
                        <label>@lang('cart::attributes.receiver_full_name') *</label>
                        <input type="text" wire:model.defer="addressForm.receiver_name" class="comment-input">
                        @error('receiver_name')
                            <span class="comment-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="comment-field-group">
                        <label>@lang('cart::attributes.receiver_mobile') *</label>
                        <input type="text" wire:model.defer="addressForm.receiver_mobile" class="comment-input">
                        @error('receiver_mobile')
                            <span class="comment-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="comment-form-grid">
                    <div class="comment-field-group">
                        <label>@lang('cart::attributes.province') *</label>

                        <select wire:change="provinceChanged" wire:model.defer="addressForm.province_id"
                            class="comment-input">
                            <option value="">{{__('core::messages.select_one_item')}}</option>
                            @foreach ($provinces as $province)
                                <option value="{{$province->id}}">{{$province->name}}</option>
                            @endforeach
                        </select>
                        @error('addressForm.province_id')
                            <span class="comment-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="comment-field-group">
                        <label>@lang('cart::attributes.city') *</label>
                        <select wire:model.defer="addressForm.city_id" class="comment-input">
                            <option value="">{{__('core::messages.select_one_item')}}</option>
                            @foreach ($cities as $city)
                                <option value="{{$city->id}}">{{$city->name}}</option>
                            @endforeach
                        </select>
                        @error('city_id')
                            <span class="comment-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="comment-form-grid">
                    <div class="comment-field-group">
                        <label>@lang('cart::attributes.postal_code') *</label>
                        <input type="text" wire:model.defer="addressForm.postal_code" class="comment-input">
                        @error('postal_code')
                            <span class="comment-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="comment-field-group">
                    <label>@lang('cart::attributes.address') *</label>
                    <textarea wire:model.defer="addressForm.address" rows="4"
                        class="comment-input comment-textarea"></textarea>
                    @error('addressForm.address')
                        <span class="comment-error">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="btn-submit-comment">@lang('cart::attributes.store_address')</button>
                </div>
            </form>
        </div>
    @endif
</div>

@push('styles')
    @vite('Modules/Shop/resources/assets/css/comments.css')
@endpush
