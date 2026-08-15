<?php

namespace Modules\Transactions\Http\Livewire\User;

use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Transactions\Entities\Transaction;

class UserTransactionShow extends UserBaseComponent
{
    // use AuthorizesRequests;
    use LivewireNotify;

    public $transaction;

    public $currency;

    public function mount(Transaction $transaction)
    {
        // $this->authorize('orders_show');
        $this->transaction = $transaction;
        $transaction->load('order');

        $this->currency = app(SettingHelper::class)->currencyLabel();
    }

    public function render()
    {
        return $this->renderView('Transactions::livewire.admin.transactions.transaction-show')
            ->layoutData([
                'title' => __('transactions::attributes.transactions_show'),
            ]);
    }
}
