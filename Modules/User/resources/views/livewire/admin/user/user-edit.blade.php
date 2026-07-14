<div class="p-6 bg-white">
    <h2 class="text-2xl font-bold mb-4">{{ __('user::attributes.edit') }} {{$user->name}}</h2>

    @if (session()->has('message'))
        <div class="mb-4 text-green-600">{{ session('message') }}</div>
    @endif
    @if ($message)
        <div class="mb-4 text-green-600">{{ $message }}</div>
    @endif
    <form wire:submit="update">
        <div class="mb-4">
            <label class="block mb-1 text-sm">{{ __('user::attributes.name') }}:</label>
            <input type="text" wire:model.defer="form.name" class="border border-gray-300 rounded px-3 py-1 w-full" />
            @error('form.name')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 text-sm">{{ __('user::attributes.avatar') }} :</label>
            <input type="file" wire:model="form.image">
            @error('form.image')
                <span class="text-red-500">{{ $message }}</span>
            @enderror

            @if (isset($form['image']) && is_object($form['image']) && method_exists($form['image'], 'temporaryUrl'))
                <img src="{{ $form['image']->temporaryUrl() }}" class="w-20 rounded mt-2">
            @else
                <img src="{{ $user->avatar?->getThumbnailUrl('small') }}" class="w-20 rounded mt-2">
            @endif
        </div>
        <div class="mb-4">
            <label class="block mb-1 text-sm">{{ __('user::attributes.password') }} : </label>
            <input type="password" wire:model.defer="form.password"
                class="border border-gray-300 rounded px-3 py-1 w-full" />
            @error('form.password')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 text-sm">{{ __('user::attributes.password_confirmation') }} : </label>
            <input type="password" wire:model.defer="form.password_confirmation"
                class="border border-gray-300 rounded px-3 py-1 w-full" />
            @error('form.password_confirmation')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-sm font-medium">{{ __('user::attributes.status') }}
                </label>
                <label class="flex items-center" for="has_fabric">
                    <input type="checkbox" wire:model="form.active" class="ml-2" />
                    {{ __('user::attributes.active') }}
                </label>
            </div>
        </div>
        <x-Core::button type="submit"
            class="bg-[#20BF86] font-semibold text-white hover:bg-[#1a9f72] disabled:opacity-40">
            @lang('user::attributes.store')
        </x-Core::button>
    </form>
</div>