<?php

namespace Nxmad\Larapay\Traits;

use Nxmad\Larapay\Models\Transaction;

trait Transactions
{
    /**
     * Setup transaction for subject without saving to database.
     *
     * @param float  $amount
     * @param array  $meta
     * @param string $state
     *
     * @return mixed
     */
    public function setup(float $amount, $meta = [], string $state = Transaction::STATE_PENDING): Transaction
    {
        $class = $this->getTransactionClass();
        $meta = is_scalar($meta) ? ['description' => $meta] : $meta;

        /**
         * @var Transaction
         */
        $transaction = (new $class)
            ->setSubject($this)
            ->fill(compact('amount', 'meta', 'state'));
    }

    /**
     * Setup transaction and save it immediately.
     *
     * @param float  $amount
     * @param array  $meta
     * @param string $state
     *
     * @return Transaction
     */
    public function transaction(float $amount, array $meta = [], string $state = Transaction::STATE_PENDING): Transaction
    {
        $transaction = $this->setup(...func_get_args());

        return $transaction($state);
    }

    /**
     * Determine if Model's balance is enough for transaction
     *
     * @param Transaction $transaction
     *
     * @return bool
     */
    public function canAfford(Transaction $transaction): bool
    {
        $balance = self::KEEP ? $this->attributes[self::KEEP] : $this->recalculateBalance();

        return $balance >= abs($transaction->amount);
    }

    /**
     * Recalculate subject's balance.
     *
     * @return mixed
     */
    public function recalculateBalance()
    {
        return $this->transactions()
            ->where('state', Transaction::STATE_SUCCESSFUL)
            ->get()
            ->sum('amount');
    }

    /**
     * Get internal Transaction class name
     *
     * @return string
     */
    protected function getTransactionClass(): string
    {
        return app()->config->get('larapay.transaction');
    }

    /**
     * Get primary value.
     *
     * @return mixed
     */
    public function getPrimaryValue()
    {
        return $this->{$this->primaryKey};
    }

    /**
     * Get polymorphic relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function transactions()
    {
        return $this->morphMany($this->getTransactionClass(), 'subject');
    }
}
