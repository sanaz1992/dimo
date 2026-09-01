<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Core\Helpers\CodeGeneratorHelper;
use Modules\Instagram\Entities\Conversation;
use Modules\Instagram\External\Repositories\Contract\ConversationRepositoryInterface;

class ConversationService
{
    public function __construct(
        protected ConversationRepositoryInterface $conversationRepository,
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->conversationRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function firstOrCreate(array $conditions, array $data)
    {
        $data['unique_code'] = CodeGeneratorHelper::generate(get_class(new Conversation), 'unique_code');

        return $this->conversationRepository->firstOrCreate($conditions, $data);
    }

    public function findByColumn($col, $value)
    {
        return $this->conversationRepository->findByColumn($col, $value);
    }

    public function findOrFail($id): Conversation
    {
        return $this->conversationRepository->findOrFail($id);
    }

    public function create(array $data): Conversation
    {
        $data['unique_code'] = CodeGeneratorHelper::generate(get_class(new Conversation), 'unique_code');

        return DB::transaction(function () use ($data) {
            $conversation = $this->conversationRepository->create($data);

            return $conversation;
        });
    }

    public function updateOrCreate(array $condition, array $data)
    {
        $data['unique_code'] = CodeGeneratorHelper::generate(get_class(new Conversation), 'unique_code');

        return $this->conversationRepository->updateOrCreate(
            $condition,
            $data
        );
    }

    public function update(Conversation $conversation, array $data): Conversation
    {
        return DB::transaction(function () use ($conversation, $data) {
            $conversation = $this->conversationRepository->update($conversation, $data);

            return $conversation;
        });
    }
}
