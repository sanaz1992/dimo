<x-guest-layout :title="__('jetstream::attributes.login_title')">
    <?php $settingHelper = app(\Modules\Core\Helpers\SettingHelper::class); ?>
    <div id="container" class="close">
        <div class="login">
            <div class="content" style="overflow: scroll;">
                <h1>ورود</h1>
                <x-jetstream::validation-errors />
                <form class="mt-8 space-y-6" method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <input type="text" name="mobile" placeholder="موبایل">

                    <input type="password" name="password" placeholder="رمز عبور">

                    <div style="display: inline-flex;">
                        <input type="text" name="captcha" placeholder="کد امنیتی">
                        <livewire:jetstream-captcha-image />
                    </div>

                    <span class="remember">مرا به خاطر بسپار</span>
                    <span class="forget">رمز عبور را فراموش کرده‌اید؟</span>
                    <span class="clearfix"></span>
                    <!-- <button onclick="return false;">ورود</button> -->
                    <button type="submit">ورود</button>
                </form>

            </div>
        </div>
        <div class="page front">
            <div class="content">
                <svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-user-plus">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="8.5" cy="7" r="4" />
                    <line x1="20" y1="8" x2="20" y2="14" />
                    <line x1="23" y1="11" x2="17" y2="11" />
                </svg>
                <h1>سلام، دوست من!</h1>
                <p>اطلاعات شخصی خود را وارد کنید و سفر خود را با ما آغاز کنید</p>
                <a href="{{ route('register.form') }}" class="button border-white">ثبت نام </a>
            </div>
        </div>

    </div>
</x-guest-layout>
