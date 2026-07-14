<nav class="mb-6">
    <div class="rounded-2xl bg-white p-8 shadow-box flex flex-col gap-2 items-center">
        <div class="relative flex justify-between items-center w-[95%]">
            <!-- Gray track -->
            <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-gray-200 z-0"></div>

            <!-- Filled track -->
            <div class="absolute top-1/2 left-0 h-0.5 bg-[#A0652E] z-10 transition-all duration-500"
                style="width: {{ ($step - 1) / ($maxStep - 1) * 100 }}%"></div>

            <!-- Step circles -->
            @for($i = 1; $i <= $maxStep; $i++)
                <div class="relative z-20">
                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center transition-colors
                                                                                                                                    {{ $step > $i ? 'bg-[#A0652E]' : ($step === $i ? 'bg-[#A0652E]' : 'bg-gray-200') }}">
                        @if($step > $i)
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            <div class="w-2 h-2 rounded-full bg-white"></div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>

        <!-- Step titles -->
        <div class="mt-4 flex justify-between w-full">
            @foreach($steps as $index => $title)
                <span class="text-sm font-semibold w-1/3 {{ $step >= $index ? 'text-gray-800' : 'text-gray-500' }}">
                    {{$title}}
                </span>
            @endforeach
        </div>
    </div>
</nav>