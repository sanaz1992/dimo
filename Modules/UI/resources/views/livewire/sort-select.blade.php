<div class="relative flex-1 sm:flex-initial min-w-[180px] sm:min-w-[200px]">
    <select wire:model.live="sortBy"
        class="w-full appearance-none bg-white border border-black/10 px-3 sm:px-4 py-2 pr-8 sm:pr-10 rounded-lg text-sm font-medium text-gray-700 cursor-pointer">
        @foreach ($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>

    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-2 text-gray-700">
        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
        </svg>
    </div>
</div>
