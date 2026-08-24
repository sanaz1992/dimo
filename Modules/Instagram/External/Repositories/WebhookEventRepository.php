<?php

namespace Modules\Instagram\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Instagram\Entities\WebhookEvent;
use Modules\Instagram\External\Repositories\Contract\WebhookEventRepositoryInterface;

class WebhookEventRepository extends BaseRepository implements WebhookEventRepositoryInterface
{
    public function __construct(WebhookEvent $model)
    {
        parent::__construct($model);
    }
}
