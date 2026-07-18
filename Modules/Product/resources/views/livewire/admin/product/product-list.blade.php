<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="__('product::attributes.products')">
        <x-slot:icon>
            <img src="{{ asset('icons\sidebar\products.svg') }}" alt="products" />
        </x-slot:icon>
        <x-dashboard::buttons.primary-action id="btn-add-product" tag="a" href="{{ route('admin.products.create') }}">
            <x-slot:icon>
                <img src="{{ asset('icons/header/add.svg') }}" alt="products" />
            </x-slot:icon>
            @lang('product::attributes.create_product')
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

                    <x-dashboard::table.cell :label="__('product::attributes.title')">
                        <span class="user-dot bg-blue-100 text-brand-blue">س</span>
                        {{$product->name}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('product::attributes.category')">
                        {{$product->category->name}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('product::attributes.created_at')">
                        {{$product->created_at_jalali_date}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('product::attributes.status')">
                        <span class="chip chip-ok">موفق</span>
                    </x-dashboard::table.cell>

                    <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                        <div class="flex gap-1">
                            <a class="row-btn mt-3 justify-center" href="{{ route('admin.products.edit', $product) }}">
                                <span>
                                    <img src="{{ asset('icons/dashboard/vuesax/outline/edit-2.svg') }}" alt="add"
                                        class="w-5" />
                                </span>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-slot:body>
    </x-dashboard::table.table>
</section>
