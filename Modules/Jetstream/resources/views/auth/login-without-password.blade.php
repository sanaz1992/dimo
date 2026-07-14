<x-guest-layout :title="__('jetstream::attributes.login_title')">
    <?php $settingHelper = app(\Modules\Core\Helpers\SettingHelper::class); ?>
    <div
        class="bg-[#3E3E3B] w-full md:w-1/2 md:rounded-3xl p-8 flex flex-col gap-8 items-center h-[400px] md:h-[600px]">
        <h2 class="text-[18px] sm:text-[20px] md:text-[24px] text-white text-center">
            {{ $settingHelper->setting('login_title_rigth_box')?->value }}
            <br> {{ $settingHelper->setting('login_text1_rigth_box')?->value }}
        </h2>
        <span class="text-[14px] sm:text-[15px] md:text-[16px] text-white/90 text-center px-4">
            {{ $settingHelper->setting('login_text2_rigth_box')?->value }}
        </span>
        <div class="flex items-center justify-center">
            <div
                class="bg-white z-10 rounded-xl w-[200px] aspect-square ml-[-50px] mb-[-150px] shadow-[0px_4px_4px_0px_#00000040] hidden md:block">
                <img src="{{$settingHelper->setting('login_img_1')?->main_image?->getThumbnailUrl('small') ?? asset('build/images/200.jpg') }}"
                    alt="logo" class="w-64 hidden md:block rounded-xl" />
            </div>
            <div class="bg-white rounded-xl w-[200px] aspect-square hidden md:block">
                <img src="{{$settingHelper->setting('login_img_2')?->main_image?->getThumbnailUrl('small') ?? asset('build/images/1.jpg') }}"
                    alt="logo" class="w-64 hidden md:block rounded-xl" />
            </div>
        </div>
    </div>
    <div class="w-full md:w-1/2 p-4 md:p-0 mt-[-200px] md:mt-0">
        <div
            class="flex flex-col justify-center py-12 px-6 md:px-8 w-full bg-white md:bg-transparent rounded-3xl shadow-hard-sm md:shadow-none">
            <div class="w-full flex flex-col gap-4 items-center">
                <img src="{{$settingHelper->setting('logo')?->main_image?->getThumbnailUrl('small') ?? asset('core/images/logo.webp') }}"
                    alt="logo" class="w-64 hidden md:block" />
                <h1 class="text-[20px] sm:text-[22px] md:text-[24px] font-bold text-gray-800 text-center">
                    {{ $settingHelper->setting('login_title_left_box')?->value }}
                </h1>
                <p class="text-[14px] sm:text-[15px] md:text-[16px] text-gray-600 text-center">
                    @lang('jetstream::messages.please_login_to_your_account')
                </p>
            </div>
            <x-jetstream::validation-errors class="mb-4" />
            <form class="mt-8 space-y-6" method="POST" action="{{ route('login.send.code') }}">
                @csrf
                <div class="flex flex-col gap-1">
                    <label for="mobile" class="block text-sm font-medium text-gray-700">
                        @lang('jetstream::messages.enter_your_mobile')
                    </label>
                    <div class="mt-1">
                        <input dir="ltr" id="mobile" name="mobile" type="text" required value="{{ old('mobile') }}"
                            placeholder=" @lang('jetstream::attributes.your_mobile_example')"
                            class="appearance-none block w-full px-3 py-2 text-[14px] sm:text-base border border-gray-300 rounded-md shadow-soft-xs placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>
                    @error('mobile')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- دکمه ورود -->
                <div>
                    <button type="submit"
                        class="w-full flex justify-center items-center py-2 px-4 rounded-md font-medium text-white bg-black">
                        @lang('jetstream::attributes.login')
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
