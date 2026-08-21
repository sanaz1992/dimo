@props([
    'title'=>null,
    'subTitle'=>null,
])

<div
    class="table-toolbar relative z-[1] mb-4 flex flex-col gap-3 sm:mb-5 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-4">
    <div class="flex min-w-0 items-center gap-3">
        @isset($icon)
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-brand-blue/10 text-brand-blue anim-pop sm:h-11 sm:w-11">
                <span>
                    {{ $icon }}
                </span>
            </div>
        @endisset
        <div class="min-w-0">
            <h2 class="text-base font-bold text-ink sm:text-lg">{{$title}}</h2>
            @isset($subTitle)
                <p class="text-[11px] text-ink-faint sm:text-[12px]">{{$subTitle}}</p>
            @endisset
        </div>
    </div>
    <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
        {{ $slot }}
    </div>

</div>
