<section class="rtl">
    <div class="flex flex-col gap-6 bg-white p-6 rounded-2xl shadow-box">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-lg md:text-xl">
                @lang('order::attributes.import_orders')
            </h2>
            <a href="{{ route('admin.orders.index') }}"
                class="text-sm text-gray-600 hover:text-gray-900 whitespace-nowrap">
                &larr; @lang('order::attributes.order_list')
            </a>
        </div>

        {{-- Upload form --}}
        <div class="rounded-xl border border-gray-200 p-4 sm:p-6 flex flex-col gap-4">
            <div>
                <label class="mb-2 block text-sm font-medium">@lang('order::attributes.excel_file')</label>
                <input wire:model="file" type="file" accept=".xlsx,.xls"
                    class="w-full rounded-lg border border-gray-300 p-2 text-sm" />
                <div wire:loading wire:target="file" class="text-xs text-gray-500 mt-1">
                    @lang('core::messages.image.uploading')...
                </div>
                @error('file')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model="fresh" class="rounded border-gray-300" />
                @lang('order::attributes.import_fresh')
            </label>

            <div class="flex items-center gap-3">
                <button wire:click="preview" wire:loading.attr="disabled" wire:target="preview,file"
                    class="bg-[#3E3E3B] text-white px-4 py-2 rounded-xl font-bold disabled:opacity-50">
                    <span wire:loading.remove wire:target="preview">@lang('order::attributes.preview_import')</span>
                    <span wire:loading wire:target="preview">@lang('core::messages.submitting')</span>
                </button>
                @if ($report)
                    <button wire:click="resetImport"
                        class="px-4 py-2 rounded-xl border border-gray-300 text-sm">
                        @lang('order::attributes.import_reset')
                    </button>
                @endif
            </div>
        </div>

        {{-- Dry-run / result report --}}
        @if ($report)
            <div class="rounded-xl border {{ $imported ? 'border-green-300 bg-green-50' : 'border-amber-300 bg-amber-50' }} p-4 sm:p-6 flex flex-col gap-4">

                <div class="flex items-center gap-2 font-semibold">
                    @if ($imported)
                        <span class="text-green-700">✅ @lang('order::messages.import_success')</span>
                    @else
                        <span class="text-amber-700">🔍 @lang('order::attributes.import_preview_title')</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 text-sm">
                    @php
                        $stats = [
                            'orders_created_label' => $report['orders_created'],
                            'items_created_label' => $report['items_created'],
                            'fabric_items_label' => $report['fabric_items_created'],
                            'stub_products_label' => count($report['stub_products_created']),
                            'deleted_orders_label' => $report['deleted_orders'],
                            'skipped_rows_label' => $report['skipped_rows'],
                            'skipped_orders_label' => count($report['skipped_orders']),
                            'duplicate_orders_label' => count($report['duplicate_orders']),
                            'unmapped_colors_label' => count($report['unmapped_colors']),
                        ];
                    @endphp
                    @foreach ($stats as $key => $value)
                        <div class="bg-white rounded-lg border border-gray-200 px-3 py-2 flex flex-col">
                            <span class="text-xs text-gray-500">@lang('order::attributes.' . $key)</span>
                            <span class="text-lg font-bold">{{ number_format($value) }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Detail lists --}}
                @php
                    $detailLists = [
                        'stub_products_label' => $report['stub_products_created'],
                        'skipped_orders_label' => $report['skipped_orders'],
                        'unmapped_colors_label' => $report['unmapped_colors'],
                    ];
                @endphp
                @foreach ($detailLists as $labelKey => $items)
                    @if (!empty($items))
                        <details class="text-sm">
                            <summary class="cursor-pointer font-medium">
                                @lang('order::attributes.' . $labelKey) ({{ count($items) }})
                            </summary>
                            <ul class="list-disc pr-6 mt-2 space-y-1 text-gray-700 max-h-48 overflow-auto">
                                @foreach ($items as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </details>
                    @endif
                @endforeach

                {{-- Confirm / import action --}}
                @if (!$imported)
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 pt-2 border-t border-amber-200">
                        <p class="text-sm text-amber-800">
                            @lang('order::attributes.import_confirm_hint')
                        </p>
                        <button wire:click="confirmImport" wire:loading.attr="disabled" wire:target="confirmImport"
                            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-xl font-bold disabled:opacity-50 whitespace-nowrap">
                            <span wire:loading.remove wire:target="confirmImport">@lang('order::attributes.confirm_import')</span>
                            <span wire:loading wire:target="confirmImport">@lang('core::messages.submitting')</span>
                        </button>
                    </div>
                @else
                    <a href="{{ route('admin.orders.index') }}"
                        class="bg-[#3E3E3B] text-white px-5 py-2 rounded-xl font-bold w-fit">
                        @lang('order::attributes.order_list')
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>
