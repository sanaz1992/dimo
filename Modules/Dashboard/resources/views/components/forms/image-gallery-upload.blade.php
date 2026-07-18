@props([
    'label' => null,
    'name' => null,
    'previews' => [],
    'accept' => 'image/*',
    'hint' => null,
])

<div
    x-data="{
        fileNames: [],
        previewUrls: @js($previews),
        updateFiles(event) {
            const files = Array.from(event.target.files || []);
            this.fileNames = files.map(file => file.name);

            const newPreviews = files
                .filter(file => file.type.startsWith('image/'))
                .map(file => URL.createObjectURL(file));

            this.previewUrls = newPreviews.length ? newPreviews : @js($previews);
        },
        clearFiles(input) {
            this.fileNames = [];
            this.previewUrls = [];
            input.value = '';
        }
    }"
    class="space-y-2"
>
    @if($label)
        <label class="block text-sm font-medium text-slate-700">
            @lang($label)
        </label>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <!-- بخش هدر: دکمه انتخاب و دکمه حذف کلی -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
            <div class="flex items-center gap-3">
                <label class="cursor-pointer">
                    <input
                        type="file"
                        accept="{{ $accept }}"
                        multiple
                        {{ $attributes->merge(['class' => 'hidden']) }}
                        @change="updateFiles($event)"
                        x-ref="input"
                    >
                    <div class="inline-flex items-center gap-2 rounded-lg bg-primary/10 px-4 py-2 text-sm font-semibold text-primary transition hover:bg-primary/20">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        افزودن به گالری
                    </div>
                </label>

                @if($hint)
                    <span class="text-[11px] text-slate-400">@lang($hint)</span>
                @else
                    <span class="text-[11px] text-slate-400">انتخاب چندگانه آزاد است</span>
                @endif
            </div>

            <button
                type="button"
                class="text-xs font-medium text-red-500 hover:text-red-700"
                @click="clearFiles($refs.input)"
                x-show="previewUrls.length"
            >
                پاکسازی همه
            </button>
        </div>

        <!-- بخش پیش‌نمایش شبکه ای (Grid) -->
        <div class="min-h-[100px]">
            <template x-if="previewUrls.length">
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">
                    <template x-for="(url, index) in previewUrls" :key="index">
                        <div class="group relative aspect-square overflow-hidden rounded-lg border border-slate-200 bg-slate-50 transition hover:border-primary">
                            <img :src="url" class="h-full w-full object-cover">
                            <!-- Overlay کوچک برای نمایش روی هاور (اختیاری) -->
                            <div class="absolute inset-0 bg-black/5 opacity-0 transition group-hover:opacity-100"></div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- حالت خالی (Empty State) -->
            <template x-if="!previewUrls.length">
                <div class="flex flex-col items-center justify-center py-4 text-slate-400">
                    <svg class="h-8 w-8 mb-2 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6.75a1.5 1.5 0 00-1.5-1.5H3.75a1.5 1.5 0 00-1.5 1.5v12.75a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <span class="text-xs">هنوز تصویری برای گالری انتخاب نشده است</span>
                </div>
            </template>
        </div>

        <!-- لیست نام فایل‌ها (اختیاری - برای اطمینان کاربر) -->
        <template x-if="fileNames.length">
            <div class="mt-4 border-t border-slate-50 pt-3">
                <p class="text-[10px] font-bold text-slate-500 mb-1">فایل‌های آماده آپلود:</p>
                <div class="flex flex-wrap gap-x-4 gap-y-1">
                    <template x-for="(fileName, index) in fileNames" :key="index">
                        <span class="text-[10px] text-slate-400 flex items-center gap-1">
                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                            <span x-text="fileName" class="truncate max-w-[150px]"></span>
                        </span>
                    </template>
                </div>
            </div>
        </template>
    </div>

    @if($name)
        @error($name)
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    @endif
</div>


{{--**************************** for create form *************************--}}
{{-- <x-dashboard::forms.image-gallery-upload
    label="product::attributes.gallery"
    name="images"
    wire:model="images"
    hint="چند تصویر برای گالری محصول انتخاب کنید"
/> --}}

{{--******************************* for update form *******************************--}}
{{-- <x-dashboard::forms.image-gallery-upload
    label="product::attributes.gallery"
    name="images"
    wire:model="images"
    :previews="$product->gallery_urls"
/> --}}

