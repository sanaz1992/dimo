<div class="container mx-auto px-4 rtl">
    <!-- Main product wizard container with Alpine.js component -->
    <div class="w-full">

        <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm">
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div>
                <label class="mb-2 block text-sm font-medium">فایل اکسل</label>
                <input wire:model="file" type="file" class="w-full rounded-lg border-gray-300" />
                @error('file')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button wire:click="import" class="btn btn-success m-5">
                ایمپورت
            </button>

        </section>
    </div>
</div>
