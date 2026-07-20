<?php

namespace Modules\Jetstream\Http\Livewire;

use Livewire\Component;

class CaptchaImage extends Component
{
    public $captchaSrc;

    public $configKey;

    public function mount($configKey = 'jetstreamcaptcha.captcha')
    {
        $this->configKey = $configKey;
        $this->refreshCaptcha();
    }

    public function refreshCaptcha()
    {
        $this->captchaSrc = captcha_src();
    }

    public function render()
    {
        return view('Jetstream::livewire.captcha-image');
    }
}
