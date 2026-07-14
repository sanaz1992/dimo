<div>
    <?php $settingHelper = app(\Modules\Core\Helpers\SettingHelper::class); ?>
    <nav class="bg-white shadow-box rounded-3xl mx-auto py-2 md:py-4">
        <div class="px-2 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center w-full h-16">
                <!-- سمت چپ: لوگو و لینک‌های ناوبری -->
                <div class="flex items-center">
                    <a href="{{route('seller.dashboard')}}">
                        <img src="{{$settingHelper->setting('logo')?->main_image?->getThumbnailUrl('small') ?? asset('build/images/logo.webp')}}"
                            alt="logo" class="w-24 md:w-32" />
                    </a>
                </div>

                {{-- <div class="relative w-52 md:w-96">
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-5">
                        <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                        </svg>
                    </div>
                    <input type="search" placeholder="در انبار جستجو کنید"
                        class="block w-full rounded-full bg-[#F7F8F8] py-3 pl-4 pr-12 border-none text-xs md:text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#3E3E3B] focus:border-transparent transition font-lahzeh-medium text-right" />
                </div> --}}
                <div class="hidden md:ml-6 md:flex md:items-center">
                    <a type="button" href="{{route('seller.orders.create')}}"
                        class="bg-[#3E3E3B] flex items-center gap-2 px-4 py-2 rounded-xl text-white focus:outline-none font-bold">
                        <img src="{{ asset('build/images/icons/header/add.svg') }}" alt="add" class="w-5" />
                        <span class="">سفارش جدید</span>
                    </a>
                </div>
                <!-- دکمه منوی موبایل برای نمایش/مخفی کردن نوار کناری -->
                <div class="-mr-2 flex items-center lg:hidden">
                    <button type="button" id="mobile-menu-button"
                        class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                        <span class="sr-only">باز کردن منوی اصلی</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</div>
