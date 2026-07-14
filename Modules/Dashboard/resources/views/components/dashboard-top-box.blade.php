<a href="{{ $route ?? '#' }}"
    class="p-4 flex flex-col md:flex-row items-center gap-4 md:bg-white md:rounded-xl md:shadow-box hover:bg-gray-100">
    <div class="flex items-center justify-center p-2 md:p-4 {{ $color }} rounded-xl">
        <img src="{{ asset('build/images/icons/dashboard/' . $icon . '.svg') }}" alt="" class="w-8 h-8" />
    </div>
    <div class="flex flex-col gap-2">
        <div class="flex flex-col items-center md:items-start text-[12px] md:text-[14px] gap-2 font-bold">
            <div class="whitespace-nowrap">{{$orderCount}}
                <span class="hidden md:inline">
                    -
                </span>
                @lang('dashboard::attributes.orders')
            </div>
            <span class="text-center md:text-start text-[12px] md:text-[14px] bg-blue-50 text-blue-700 inset-ring-blue-700/10 inline-flex items-center justify-center rounded-full px-3 py-1 font-medium whitespace-nowrap">
                {{$title}}
            </span>
        </div>
    </div>
</a>