<div>
    @can('orders_list')
        <!-- Top Boxes -->
        <section>
            <div>
                <x-dashboard-top-boxes panel="admin" />
            </div>
        </section>

        <!-- Dashboard Orders -->
        <section class="bg-white p-4 md:p-6 rounded-xl shadow-box mt-6">
            <div class="flex flex-row items-center justify-between gap-3 sm:gap-4 mb-4 md:mb-6">
                <h2 class="font-semibold text-lg md:text-xl mb-4 md:mb-6">
                    @lang('dashboard::attributes.collection_orders')
                </h2>
                
            </div>
            <div>
                <div class="overflow-x-auto">
                    <div class="relative overflow-visible rounded-xl border border-black/10">
                        <div class="overflow-x-auto overflow-y-visible">
                            <table class="min-w-[800px] w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            @lang('order::attributes.row')
                                        </th>
                                        <th
                                            class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            @lang('order::attributes.code')
                                        </th>
                                        <th
                                            class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            @lang('order::attributes.date')
                                        </th>
                                        <th
                                            class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            @lang('order::attributes.saller_name')
                                        </th>
                                        <th
                                            class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            @lang('order::attributes.order_qty')
                                        </th>
                                        <th
                                            class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            @lang('order::attributes.city')
                                        </th>
                                        <th
                                            class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            @lang('order::attributes.status')
                                        </th>
                                        <th
                                            class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">

                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            {{__('core::messages.without_item')}}
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endcan

</div>
