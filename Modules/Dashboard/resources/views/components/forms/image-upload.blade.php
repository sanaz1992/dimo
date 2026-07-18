@props([
    'label' => null,
    'name' => null,
    'preview' => null,
    'fileName' => null,
    'accept' => 'image/*',
    'hint' => null,
    'removeMethod' => 'removeImage',
    'uploadKey' => null,
])

<div class="space-y-2">
    @if($label)
        <label class="block text-sm font-medium text-slate-700">
            @lang($label)
        </label>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">

            <div class="relative h-24 w-24 flex-shrink-0 overflow-hidden rounded-lg border border-dashed border-slate-300 bg-slate-50">
                @if($preview)
                    <img src="{{ $preview }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full flex-col items-center justify-center p-2 text-center">
                        <svg class="h-6 w-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6.75a1.5 1.5 0 00-1.5-1.5H3.75a1.5 1.5 0 00-1.5 1.5v12.75a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        <span class="mt-1 text-[10px] leading-tight text-slate-400">بدون تصویر</span>
                    </div>
                @endif

                <div
                    wire:loading.flex
                    wire:target="{{ $attributes->wire('model')->value() }}"
                    class="absolute inset-0 items-center justify-center bg-white/70 text-xs font-semibold text-slate-500"
                >
                    در حال آپلود...
                </div>
            </div>

            <div class="flex min-w-0 flex-1 flex-col justify-center">
                <div class="flex items-center gap-3">
                    <label class="cursor-pointer">
                        <input
                            type="file"
                            accept="{{ $accept }}"
                            @if($uploadKey) wire:key="image-upload-{{ $uploadKey }}" @endif
                            {{ $attributes->merge(['class' => 'hidden']) }}
                        >

                        <div class="inline-flex items-center gap-2 rounded-lg bg-primary/10 px-4 py-2 text-sm font-semibold text-primary transition hover:bg-primary/20">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            انتخاب تصویر
                        </div>
                    </label>

                    @if($preview || $fileName)
                        <button
                            type="button"
                            wire:click="{{ $removeMethod }}"
                            class="text-xs font-medium text-red-500 hover:text-red-700"
                        >
                            حذف
                        </button>
                    @endif
                </div>

                <div class="mt-2 flex flex-col gap-1">
                    @if($fileName)
                        <p class="truncate text-xs text-slate-500">
                            {{ $fileName }}
                        </p>
                    @endif

                    @if($hint)
                        <p class="text-[11px] text-slate-400">@lang($hint)</p>
                    @else
                        <p class="text-[11px] text-slate-400">PNG, JPG, WEBP حداکثر 2MB</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @if($name)
        @error($name)
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    @endif
</div>



{{--**************************** for create form **********************--}}
{{-- <x-dashboard::forms.image-upload
    label="product::attributes.image"
    name="image"
    wire:model="image"
    hint="فرمت‌های مجاز: png, jpg, webp / حداکثر 2MB"
/> --}}


{{--***************************** for update form ****************************--}}
{{-- <x-dashboard::forms.image-upload
    label="product::attributes.image"
    name="image"
    wire:model="image"
    :preview="$product->image_url"
/> --}}

