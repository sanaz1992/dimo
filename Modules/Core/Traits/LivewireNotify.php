<?php

namespace Modules\Core\Traits;

trait LivewireNotify
{
    public function notify(string $type, string $message): void
    {
        $this->dispatch('notify', type: $type, message: $message);
    }
}
