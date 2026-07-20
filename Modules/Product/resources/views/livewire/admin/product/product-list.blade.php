<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="__('product::attributes.products')">
        <x-slot:icon>
            <img src="{{ asset('icons\sidebar\products.svg') }}" alt="products" />
        </x-slot:icon>
        <x-dashboard::buttons.primary-action id="btn-add-product" tag="a" class="btn-fill btn-new-tx shrink-0"
            href="{{ route('admin.products.create') }}">
            <x-slot:icon>
                <img src="{{ asset('icons/header/add.svg') }}" alt="products" />
            </x-slot:icon>
            @lang('product::attributes.create_product')
        </x-dashboard::buttons.primary-action>
        <x-dashboard::buttons.primary-action id="btn-add-product" tag="a" class="btn-fill btn-new-tx shrink-0"
            href="{{ route('admin.product.categories.index') }}">
            @lang('product::attributes.product_categories')
        </x-dashboard::buttons.primary-action>
    </x-dashboard::card.card-header>


    <x-dashboard::table.table>
        <x-slot:head>
            <tr>
                <th>@lang('core::attributes.row')</th>
                <th>@lang('product::attributes.product_code')</th>
                <th>@lang('product::attributes.title')</th>
                <th>@lang('product::attributes.category')</th>
                <th>@lang('product::attributes.created_at')</th>
                <th>@lang('product::attributes.status')</th>
                <th class="col-actions"></th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @foreach ($products as $product)
                <tr class="data-row" data-searchable="" data-status="success" style="animation-delay:0.35s">
                    <x-dashboard::table.cell :label="__('core::attributes.row')">
                        {{ $loop->index + 1 }}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('product::attributes.product_code')">
                        {{$product->code}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('product::attributes.title')" class="flex items-center gap-2">
                        <img alt="{{$product->title}}" class="h-10 w-10 rounded-full object-cover"
                            src="{{ $product->main_image?->getThumbnailUrl('small') }}">
                        {{$product->name}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('product::attributes.category')">
                        {{$product->category->name}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('product::attributes.created_at')">
                        {{$product->created_at_jalali_date}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('product::attributes.status')">
                        @if($product->is_active)
                            <span class="chip chip-ok">@lang('product::attributes.active')</span>
                        @else
                            <span class="chip chip-fail">@lang('product::attributes.inactive')</span>
                        @endif
                    </x-dashboard::table.cell>

                    <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                        <div class="flex gap-1">
                            <x-dashboard::buttons.primary-action id="btn-edit-product-{{$product->id}}" tag="a"
                                href="{{ route('admin.products.edit', $product) }}" size="sm">
                                <img src="{{ asset('icons/dashboard/vuesax/outline/edit-2.svg') }}" alt="add" class="w-5" />
                            </x-dashboard::buttons.primary-action>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-slot:body>
    </x-dashboard::table.table>
</section>