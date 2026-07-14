{{-- <x-guest-layout>
    <x-jetstream::authentication-card>
        <x-slot name="logo">
            <x-jetstream::authentication-card-logo />
        </x-slot>

        <x-jetstream::validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-jetstream::label for="name" value="{{ __('Name') }}" />
                <x-jetstream::input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                    required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-jetstream::label for="mobile" value="{{ __('Mobile') }}" />
                <x-jetstream::input id="mobile" class="block mt-1 w-full" type="text" name="mobile"
                    :value="old('mobile')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-jetstream::label for="password" value="{{ __('Password') }}" />
                <x-jetstream::input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-jetstream::label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-jetstream::input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="mt-4">
                <x-jetstream::label for="terms">
                    <div class="flex items-center">
                        <x-jetstream::checkbox name="terms" id="terms" required />

                        <div class="ms-2">
                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms
                                of Service').'</a>',
                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy
                                Policy').'</a>',
                            ]) !!}
                        </div>
                    </div>
                </x-jetstream::label>
            </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-jetstream::button class="ms-4">
                    {{ __('Register') }}
                </x-jetstream::button>
            </div>
        </form>
    </x-jetstream::authentication-card>
</x-guest-layout> --}}

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
                {{-- <p class="text-[14px] sm:text-[15px] md:text-[16px] text-gray-600 text-center">
                    @lang('jetstream::messages.please_login_to_your_account')
                </p> --}}
            </div>
            {{-- <x-jetstream::validation-errors class="mb-4" /> --}}
            <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="mobile" value="{{ $data['mobile'] }}" />
                <div class="flex flex-col gap-1">
                    <label for="code" class="block text-sm font-medium text-gray-700">
                        @lang('jetstream::messages.enter_your_code')
                        {{ $data['mobile'] }}
                    </label>
                    <div class="mt-1">
                        <input id="code" name="code" type="text" required value="{{ old('code') }}"
                            placeholder=" @lang('jetstream::messages.enter_your_code')"
                            class="appearance-none block w-full px-3 py-2 text-[14px] sm:text-base border border-gray-300 rounded-md shadow-soft-xs placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>
                    @error('code')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-1">
                    <label for="captcha" class="block text-sm font-medium text-gray-700">
                        @lang('jetstream::messages.enter_captcha')
                    </label>
                    <div class="mt-1 " style="display: inline-flex;">
                        <input id="captcha" name="captcha" type="text" required
                            placeholder=" @lang('jetstream::messages.enter_captcha')"
                            class="appearance-none block w-full pr-8 px-3 py-2 text-[14px] sm:text-base border border-gray-300 rounded-md shadow-soft-xs placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                        <livewire:jetstream-captcha-image />
                    </div>
                    @error('captcha')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- دکمه ورود -->
                <div>
                    <button type="submit"
                        class="w-full flex justify-center items-center py-2 px-4 rounded-md font-medium text-white bg-black">
                        @lang('jetstream::attributes.check_code')
                    </button>
                </div>
            </form>
            <div class="w-full flex flex-col gap-4 mt-2">
                <p class="text-[14px] sm:text-[15px] md:text-[16px] text-gray-600 ">
                    <a href="{{ route('login') }}">
                        @lang('jetstream::messages.if_you_have_account_please_use_login_link')
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
