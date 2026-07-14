<div class="{{ $wrapperClass ?: 'mb-6 bg-white p-4 rounded-3xl shadow-box overflow-x-auto' }}">
    <nav
        class="bg-offwhite border border-[#E7E7E6] rounded-xl text-dark flex rtl:space-x-reverse p-1 w-fit max-w-full overflow-x-auto">

        @foreach($tabs as $key => $label)
            <button wire:click="setActive('{{ $key }}')"
                class="whitespace-nowrap py-2 px-4 md:px-2 min-w-[140px] w-[160px] md:w-[180px] md:min-w-[unset] rounded-lg font-medium text-sm flex-shrink-0 
                        {{ $active == $key ? 'bg-white text-black' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                {{ $label }}
            </button>
        @endforeach

    </nav>
</div>