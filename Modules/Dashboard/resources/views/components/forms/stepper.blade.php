@props([
    'steps' => [],
    'currentStep' => null,
    'changeByClick'=>false
])

<div class="space-y-4">
    <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
        <div class="tabs tabs-scroll w-full sm:w-auto" role="tablist">
            @foreach ($steps as $key=>$label)
                <button
                    type="button"
                    role="tab"
                    @if ($changeByClick)
                        wire:click="goToStep('{{ $key }}')"
                    @endif
                    @class([
                        'tab',
                        'tab-on' => $currentStep === $key,
                    ])
                    aria-selected="{{ $currentStep === $key ? 'true' : 'false' }}"
                >
                    @if (!empty($step['icon']))
                        <span class="tab-icon">{!! $step['icon'] !!}</span>
                    @endif

                    <span>{{ $label }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        {{ $slot }}

        @isset($footer)
            <div class="mt-6 border-t border-slate-100 pt-4">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
