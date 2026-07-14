<div class="container mx-auto px-4 rtl">
    <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm">
        <h2 class="text-2xl font-bold mb-4">{{ __('acl::attributes.create') }}</h2>

        <form wire:submit="store">
            <div class="flex flex-col gap-4">
                <div class="grid gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium">{{ __('acl::attributes.title') }}:</label>
                            <input type="text" wire:model.defer="form.title"
                                class="w-full rounded-lg border-gray-300" />
                            @error('form.title')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium">{{ __('acl::attributes.name') }}:({{ __('acl::attributes.english') }})</label>
                            <input type="text" wire:model.defer="form.name" class="w-full rounded-lg border-gray-300" />
                            @error('form.name')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="grid gap-4 text-sm">
                        <label class="mb-2 block text-sm font-medium">{{ __('acl::attributes.permissions') }}:</label>
                        <div class=" grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach ($permissions as $permission)
                                <label class="flex items-center" for="{{ $permission->id }}">
                                    <input type="checkbox" wire:model="form.selectedPermissions" id="{{ $permission->id }}"
                                        value="{{ $permission->name }}"
                                        class="rounded border-gray-300 ml-1 text-emerald-600 focus:ring-emerald-500" />
                                    {{ $permission->title }}
                                </label>
                            @endforeach
                        </div>
                        @error('form.selectedPermissions')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-between">
                <x-Core::button type="submit" class="font-semibold bg-[#20BF86] text-white hover:bg-[#1a9f72] ">
                    {{ __('acl::attributes.store') }}
                </x-Core::button>
            </div>
        </form>
    </section>
</div>