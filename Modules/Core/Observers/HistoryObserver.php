<?php

namespace Modules\Core\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\History;

class HistoryObserver
{
    /**
     * Handle the "updated" event.
     */
    public function updated(Model $model): void
    {
        $changes = $model->getChanges(); // فقط فیلدهایی که تغییر کردند
        $original = $model->getOriginal();

        foreach ($changes as $field => $newValue) {
            // فیلدهایی که میخوای ذخیره بشن
            if (in_array($field, $model->historyFields ?? [])) {
                History::create([
                    'user_id' => Auth::id(),
                    'historiable_type' => get_class($model),
                    'historiable_id' => $model->id,
                    'field' => $field,
                    'old_value' => $original[$field] ?? null,
                    'new_value' => $newValue,
                    'action' => 'update',
                    'description' => $model->historyDescription ?? null,
                ]);
            }
        }
    }

    /**
     * Handle the "created" event.
     */
    public function created(Model $model): void
    {
        foreach ($model->historyFields ?? [] as $field) {
            History::create([
                'user_id' => Auth::id(),
                'historiable_type' => get_class($model),
                'historiable_id' => $model->id,
                'field' => $field,
                'old_value' => null,
                'new_value' => $model->{$field},
                'action' => 'create',
                'description' => $model->historyDescription ?? null,
            ]);
        }
    }

    /**
     * Handle the "deleted" event.
     */
    public function deleted(Model $model): void
    {
        foreach ($model->historyFields ?? [] as $field) {
            History::create([
                'user_id' => Auth::id(),
                'historiable_type' => get_class($model),
                'historiable_id' => $model->id,
                'field' => $field,
                'old_value' => $model->{$field},
                'new_value' => null,
                'action' => 'delete',
                'description' => $model->historyDescription ?? null,
            ]);
        }
    }
}
