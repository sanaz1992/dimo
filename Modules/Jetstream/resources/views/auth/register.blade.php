<x-guest-layout :title="__('jetstream::attributes.login_title')">
    <?php $settingHelper = app(\Modules\Core\Helpers\SettingHelper::class); ?>
    <div id="container" class="active">
        <div class="page back">
            <div class="content">
                <svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-log-out">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                <h1>خوش برگشتید!</h1>
                <p>برای اتصال با ما لطفاً با اطلاعات شخصی خود وارد شوید</p>
                <a href="{{ route('login') }}" class="button border-white">ورود
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-arrow-left-circle">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 8 8 12 12 16" />
                        <line x1="16" y1="12" x2="8" y2="12" />
                    </svg> --}}

                </a>
            </div>
        </div>
        <div class="register">
            <div class="content" style="overflow: scroll;">
                <h1>ثبت نام</h1>
                {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-facebook">
                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                </svg>
                <svg class="feather feather-twitter sc-dnqmqq jxshSx" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path
                        d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z">
                    </path>
                </svg>
                <svg class="feather feather-github sc-dnqmqq jxshSx" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path
                        d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22">
                    </path>
                </svg>
                <svg class="feather feather-linkedin sc-dnqmqq jxshSx" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                    <rect x="2" y="9" width="4" height="12"></rect>
                    <circle cx="4" cy="4" r="2"></circle>
                </svg>

                <span class="loginwith">یا</span> --}}
                <x-jetstream::validation-errors />
                <form class="mt-8 space-y-6" method="POST" action="{{ route('register.post') }}">
                    @csrf
                    <input type="text" name="name" placeholder="نام">
                    <input type="mobile" name="mobile" placeholder="موبایل">
                    <input type="password" name="password" placeholder="رمز عبور">
                    <input type="password" name="password_confirmation" placeholder="تکرار رمز عبور">
                    <div style="display: inline-flex;">
                        <input type="text" name="captcha" placeholder="کد امنیتی">
                        <livewire:jetstream-captcha-image />
                    </div>
                    {{-- <span class="remember">شرایط را می‌پذیرم</span> --}}
                    <span class="clearfix"></span>
                    <button type="submit">ثبت نام</button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
