<div class="panel cart-auth">
    <div class="cart-auth__header">
        <h2 class="cart-auth__title">ورود برای ادامه خرید</h2>
        <p class="cart-auth__description">
            برای ادامه فرایند ثبت سفارش، لطفاً وارد حساب کاربری خود شوید.
        </p>
    </div>

    <form wire:submit="login" class="cart-auth__form">
        <div class="cart-auth__field">
            <label for="mobile" class="cart-auth__label">شماره موبایل</label>
            <input id="mobile" type="text" wire:model.defer="mobile"
                class="cart-auth__input @error('mobile') is-invalid @enderror" placeholder="09xxxxxxxxx" dir="ltr">
            @error('mobile')
                <span class="cart-auth__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="cart-auth__field">
            <label for="password" class="cart-auth__label">رمز عبور</label>
            <input id="password" type="password" wire:model.defer="password"
                class="cart-auth__input @error('password') is-invalid @enderror" placeholder="رمز عبور خود را وارد کنید"
                dir="ltr">
            @error('password')
                <span class="cart-auth__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="cart-auth__actions">
            <button type="submit" class="checkout-btn" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="login">ورود و ادامه</span>
                <span wire:loading wire:target="login">در حال بررسی...</span>
            </button>
        </div>
    </form>

    <div class="cart-auth__footer">
        <p class="cart-auth__hint">
            حساب کاربری ندارید؟
            <a href="{{ route('register.form') }}" class="cart-auth__link">ثبت‌نام</a>
        </p>

        <button type="button" class="cart-auth__back" wire:click="goToCart">
            بازگشت به سبد خرید
        </button>
    </div>
</div>
