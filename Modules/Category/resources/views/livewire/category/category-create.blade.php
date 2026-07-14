<div class="p-6 bg-white">
    <h2 class="text-2xl font-bold mb-4">{{ __('category::attributes.category_create') }}</h2>

    @if (session()->has('message'))
        <div class="mb-4 text-green-600">{{ session('message') }}</div>
    @endif
    @if ($message)
        <div class="mb-4 text-green-600">{{ $message }}</div>
    @endif
    <form wire:submit="store" class="space-y-3">
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
                                        {{ $category->title }}
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
                <label class="block mb-1 text-sm">{{ __('category::attributes.title') }}:</label>
                <input type="text" wire:model.defer="form.title"
                    class="border border-gray-300 rounded px-3 py-1 w-full" />
                @error('form.title')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            {{-- <div>
                <label class="block mb-1 text-sm">{{ __('category::attributes.parent_category') }}:</label>
                <select wire:model="form.parent_id" class="w-full pr-10 pl-3 text-right rounded-lg border-gray-300">
                    <option value="">@lang('category::messages.choose')</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}"> {{ $category->title }}</option>
                    @endforeach
                </select>
                @error('form.parent_id')
                <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
                <sub class="text-gray-500">
                    {{__('category::messages.fill_parent_if_needed')}}
                </sub>
            </div> --}}
            <div>
                <label class="mb-1 block text-sm font-medium">@lang('category::attributes.form.type')</label>
                <select wire:model="form.type" class="w-full pr-10 pl-3 text-right rounded-lg border-gray-300">
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
        <div>
            <label class="block mb-1 text-sm">{{ __('category::attributes.image') }} :</label>
            <input type="file" wire:model="form.image">
            @error('form.image')
                <span class="text-red-500">{{ $message }}</span>
            @enderror

            @if (isset($form['image']) && is_object($form['image']))
                <img src="{{ $form['image']->temporaryUrl() }}" class="w-20 rounded mt-2">
            @endif
        </div>

        <x-Core::button type="submit" class="bg-[#20BF86] font-semibold text-white hover:bg-[#1a9f72]">
            {{ __('category::attributes.store') }}
        </x-Core::button>
    </form>
</div>
