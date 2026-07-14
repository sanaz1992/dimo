<?php

namespace Modules\Jetstream\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Services\SmsIr;
use Modules\Jetstream\External\Repositories\Contract\OtpRepositoryInterface;
use Modules\Jetstream\Rules\LoginCheckCodeRules;
use Modules\Jetstream\Rules\LoginRules;
use Modules\Jetstream\Rules\LoginSendCodeRules;
use Modules\Jetstream\Rules\RegisterCheckCodeRules;
use Modules\Jetstream\Rules\RegisterSendCodeRules;
use Modules\User\Enums\UserLevel;
use Modules\User\Services\UserService;

class JetstreamController
{
    public function __construct(
        protected OtpRepositoryInterface $otpRepository,
        protected UserService $userService
    ) {
    }

    public function login(LoginRules $request)
    {
        $data = $request->all();
        $user = $this->userService->findByColumn('mobile', $data['mobile']);
        if (!$user) {
            return redirect()->route('login')->withErrors(['message' => 'کاربر مورد نظر یافت نشد لطفا از طریق فرم ثبت نام اقدام نمایید.']);
        }
        if (Hash::check($data['password'], $user->password)) {
            return redirect()->route('login')->withErrors(['message' => 'رمز عبور وارد شده صحیح نیست.']);
        }
        Auth::loginUsingId($user->id);
        if ($user->level == "admin") {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }
    public function loginSendCode(LoginSendCodeRules $request)
    {
        $data = $request->all();
        $otps = $this->otpRepository->all(null, [], [], [
            'where' => [
                'mobile' => ['=', $data['mobile']],
                'expired_at' => ['>', Carbon::now()],
            ],
        ]);
        if (count($otps)) {
            return redirect()->back()->withErrors(['message' => 'از اخرین کد تاییدی که دریافت کرده اید کمتر از 2 دقیقه گذشته است.لطفا بعد از 2 دقیقه مجددا درخواست دهید.']);
        }
        $code = rand(100000, 10000000);
        $this->otpRepository->create([
            'mobile' => $data['mobile'],
            'code' => $code,
            'expired_at' => Carbon::now()->addMinutes(2),
        ]);
        $smsResult = SmsIr::sendVerify($data['mobile'], $code);
        if (! $smsResult['success']) {
            return redirect()->back()->withErrors(['message' => 'مشکلی در ارسال کد تایید رخ داده لطفا دقایقی دیگر مجددا تلاش نمایید.']);
        }

        return redirect()->route('login.check.code.form')->with('mobile', $data['mobile']);
    }

    public function showCheckCode()
    {
        $mobile = session('mobile');
        if ($mobile) {
            return view('Jetstream::auth.login-check-code', compact('mobile'));
        } else {
            return redirect()->route('login');
        }
    }

    public function loginCheckCode(LoginCheckCodeRules $request)
    {
        $data = $request->all();
        $otps = $this->otpRepository->all(null, [], [], [
            'where' => [
                'mobile' => ['=', $data['mobile']],
                'expired_at' => ['>', Carbon::now()],
            ],
        ]);
        if (! count($otps)) {
            return redirect()->route('register.form')->withErrors(['message' => 'شما کد تایید فعالی ندارید. لطفا مجددا تلاش نمایید']);
        }
        $otp = $otps->first();
        if ($otp->code != $data['code']) {
            return redirect()->back()->withErrors(['message' => 'کد تایید اشتباه است.']);
        }
        $user = resolve(UserService::class)->findByColumn('mobile', $data['mobile']);
        if (! $user) {
            $user = resolve(UserService::class)->create([
                'name' => $data['mobile'],
                'mobile' => $data['mobile'],
                'password' => $data['mobile'],
                'level' => UserLevel::ADMIN->value,
                'active' => true,
                'expired_at' => Carbon::now()->addDays(3),
            ]);
            $user->assignRole(['super_admin']);
        }
        Auth::loginUsingId($user->id);

        return redirect()->route('admin.dashboard');
    }

    public function sendCode(RegisterSendCodeRules $request)
    {
        $data = $request->all();
        $settings = cache('settings');
        if (! $settings->where('key', 'user_can_register')->first()->value) {
            return redirect()->route('login');
        }

        $user = resolve(UserService::class)->findByColumn('mobile', $data['mobile']);
        if ($user) {
            return redirect()->route('login')->withErrors(['message' => 'این شماره موبایل در سیستم ثبت شده است. لطفا از طریق لینک ورود اقدام نمایید.']);
        }
        $otps = $this->otpRepository->all(null, [], [], [
            'where' => [
                'mobile' => ['=', $data['mobile']],
                'expired_at' => ['>', Carbon::now()],
            ],
        ]);
        if (count($otps)) {
            return redirect()->back()->withErrors(['message' => 'از اخرین کد تاییدی که دریافت کرده اید کمتر از 2 دقیقه گذشته است.لطفا بعد از 2 دقیقه مجددا درخواست دهید.']);
        }
        $code = rand(100000, 10000000);
        $this->otpRepository->create([
            'mobile' => $data['mobile'],
            'code' => $code,
            'expired_at' => Carbon::now()->addMinutes(2),
        ]);
        $smsResult = SmsIr::sendVerify($data['mobile'], $code);
        if (! $smsResult['success']) {
            return redirect()->back()->withErrors(['message' => 'مشکلی در ارسال کد تایید رخ داده لطفا دقایقی دیگر مجددا تلاش نمایید.']);
        }

        return view('Jetstream::auth.register-check-code', compact('data'));
    }

    public function register(RegisterCheckCodeRules $request)
    {
        $data = $request->all();
        $otps = $this->otpRepository->all(null, [], [], [
            'where' => [
                'mobile' => ['=', $data['mobile']],
                'expired_at' => ['>', Carbon::now()],
            ],
        ]);
        if (! count($otps)) {
            return redirect()->route('register.form')->withErrors(['message' => 'شما کد تایید فعالی ندارید. لطفا مجددا تلاش نمایید']);
        }
        $otp = $otps->first();
        if ($otp->code != $data['code']) {
            return redirect()->back()->withErrors(['message' => 'کد تایید اشتباه است.']);
        }
        $user = resolve(UserService::class)->create([
            'name' => $data['mobile'],
            'mobile' => $data['mobile'],
            'password' => $data['mobile'],
            'level' => UserLevel::ADMIN->value,
            'active' => true,
            'expired_at' => Carbon::now()->addDays(3),
        ]);
        $user->assignRole(['super_admin']);
        Auth::loginUsingId($user->id);

        return redirect()->route('admin.admins.edit', $user);
    }
}
