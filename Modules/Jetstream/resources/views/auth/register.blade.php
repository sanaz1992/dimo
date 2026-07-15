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
                <a href="{{ route('login') }}" class="button border-white">ورود</a>
            </div>
        </div>
        <div class="register">
            <div class="content" style="overflow: scroll;">
                <h1>ثبت نام</h1>
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
