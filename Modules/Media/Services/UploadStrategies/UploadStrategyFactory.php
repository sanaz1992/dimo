<?php

namespace Modules\Media\Services\UploadStrategies;

class UploadStrategyFactory
{
    public function make(string $driver)
    {
        switch ($driver) {
            case 's3':
                return app(LiaraUploadStrategy::class);
            case 'public':
            case 'public_html':
                return  new LocalUploadStrategy();
            default:
                throw new \Exception("Unsupported disk");
        }
    }
}
