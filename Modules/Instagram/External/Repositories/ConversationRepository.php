<?php

namespace Modules\Instagram\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Instagram\Entities\Conversation;
use Modules\Instagram\External\Repositories\Contract\ConversationRepositoryInterface;

class ConversationRepository extends BaseRepository implements ConversationRepositoryInterface
{
    public function __construct(Conversation $model)
    {
        parent::__construct($model);
    }
}
