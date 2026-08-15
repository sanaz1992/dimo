@props([
    'steps' => [],
    'currentStep' => null,
    'changeByClick' => false
])

<div class="space-y-5">
    {{-- Header / Stepper --}}
    <div class="flex items-center justify-between px-1">
        <div class="inline-flex items-center gap-1 rounded-2xl bg-slate-100 p-1.5 shadow-inner" role="tablist">
            @foreach ($steps as $key => $step)
                @php
                    $isActive = $currentStep === $key;
                    // اگر استپ فقط یک رشته ساده باشد، آن را به عنوان لیبل در نظر بگیر
                    $label = is_array($step) ? ($step['label'] ?? '') : $step;
                    $icon = is_array($step) ? ($step['icon'] ?? null) : null;
                @endphp

                <button
                    type="button"
                    role="tab"
                    @if ($changeByClick) wire:click="goToStep('{{ $key }}')" @endif
                    @disabled(!$changeByClick && !$isActive)
                    @class([
                        'relative flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition-all duration-200',
                        'bg-white text-blue-700 shadow-sm ring-1 ring-black/5' => $isActive,
                        'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50' => !$isActive,
                        'cursor-default' => !$changeByClick,
                    ])
                    aria-selected="{{ $isActive ? 'true' : 'false' }}"
                >
                    {{-- شماره مرحله یا آیکون --}}
                    <span @class([
                        'flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-bold transition-colors',
                        'bg-blue-600 text-white' => $isActive,
                        'bg-slate-200 text-slate-500' => !$isActive,
                    ])>
                        @if($icon)
                            {!! $icon !!}
                        @else
                            {{ toPersianNumber($loop->iteration) }}
                        @endif
                    </span>

                    <span class="whitespace-nowrap">{{ $label }}</span>
                </button>
            @endforeach
        </div>

        {{-- اختیاری: می‌توانی اینجا یک دکمه کمکی یا وضعیت کلی بگذاری --}}
    </div>

    {{-- Body --}}
    <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
        <div class="min-h-[200px]">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="mt-8 flex items-center justify-between border-t border-slate-100 pt-5">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
