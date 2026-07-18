<div class="container mx-auto px-4 rtl">
    <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm">

        <h2 class="mb-4 text-xl font-bold">
            @lang('category::attributes.edit') {{$category->name}}
        </h2>
        @if ($message)
            <div class="mb-4 text-green-600">{{ $message }}</div>
        @endif
        <form wire:submit.prevent="update">
            <div class="flex flex-col gap-4">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">
                            @lang('category::attributes.image')
                        </label>
                        <p class="text-xs text-gray-500 mb-2">
                            @lang('media::attributes.image_formats'):
                            {{config('media.validations.image.mimes')}}
                            (@lang('media::attributes.max')
                            {{config('media.validations.image.max') / 1024}}
                            @lang('media::attributes.MB'))
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full flex-1">
                            <label
                                class="flex flex-col items-center justify-center w-full h-40 border rounded-3xl cursor-pointer hover:bg-gray-100 transition-colors border-[#D3E0E4] relative overflow-hidden                                                                                                                                                                                                                                                                                                                                               {{  $form['image'] || $initialImage ? 'border-green-500 bg-green-50' : '' }}">
                                @if ($form['image'] || $initialImage)
                                    <div class="w-full h-full flex items-center justify-center p-2 text-center">
                                        <div>
                                            <svg class="mx-auto h-10 w-10 text-green-500 mb-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7">
                                                </path>
                                            </svg>
                                            <p class="text-sm text-gray-600">
                                                @lang('media::attributes.image_selected')
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                @lang('media::messages.click_again_for_change_image')
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 px-4 text-center">
                                        <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500">
                                            <span class="font-semibold">
                                                @lang('media::messages.click_for_upload')
                                            </span>
                                            @lang('media::messages.or_drop_image')
                                        </p>
                                    </div>
                                @endif
                                <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    accept="image/jpeg, image/png, image/jpg" wire:model="form.image" />
                            </label>
                        </div>

                        @if ($form['image'] || $initialImage)
                            <div class="w-[200px] flex-shrink-0">
                                <div class="h-40 w-full rounded-3xl overflow-hidden border border-[#D3E0E4] relative group">
                                    <img src="{{ $this->imagePreview }}" alt="@lang('media::attributes.image_preview')"
                                        class="h-full w-full object-cover" />
                                    <div
                                        class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 text-white text-xs p-1.5 text-center truncate">
                                        {{ $this->clientOriginalName }}
                                    </div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <button type="button" wire:click="removeImage"
                                            class="p-2 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity transform hover:scale-110"
                                            title="@lang('media::attributes.delete_image')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0A1 1 0 019 6h6a1 1 0 011 1m-8 0h10" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('form.image')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($categoryDepth > 1)
                        @for ($i = 0; $i < $categoryDepth - 1; $i++)
                            <div class="mb-4">
                                <label class="block mb-1 text-sm">
                                    @lang('category::attributes.category') @lang('category::attributes.level') {{ $i+1 }}
                                </label>

                                <select wire:model.live="parents.{{ $i }}"
                                    class="w-full pr-10 pl-3 text-right rounded-lg border-gray-300">
                                    <option value="">
                                        @lang('category::messages.choose')
                                    </option>
                                    @if(!empty($categories[$i]))
                                        @foreach ($categories[$i] as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        @endfor
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium">@lang('category::attributes.name')</label>
                        <input wire:model="form.name" type="text" class="w-full rounded-lg border-gray-300"
                               placeholder="@lang('category::messages.enter_name')" />
                        @error('form.name')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label
                                class="mb-2 block text-sm font-medium">@lang('category::attributes.form.type')</label>
                        <select wire:model="form.type"
                                class="w-full pr-10 pl-3 text-right rounded-lg border-gray-300">
                            <option value="">@lang('category::messages.choose')</option>
                            @foreach ($types as $i => $type)
                                <option value="{{ $type->value }}" wire:key="{{$type->value}}">
                                    {{$type->label()}}
                                </option>
                            @endforeach
                        </select>
                        @error('form.type')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>
            <div class="mt-6 flex items-center justify-between">
                <x-Core::button type="submit"
                    class="bg-[#20BF86] font-semibold text-white hover:bg-[#1a9f72] disabled:opacity-40">
                    @lang('category::attributes.store')
                </x-Core::button>
            </div>
        </form>


    </section>
</div>

@push('scripts')
    @vite('Modules/Core/resources/assets/js/utils.js')
@endpush
