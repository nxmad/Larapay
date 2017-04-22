<?php

namespace Skylex\Larapay\Traits;

use Skylex\Larapay\Models\Transaction;

trait Transactions
{
    /**
     * Setup transaction for subject without saving to database.
     *
     * @param int    $amount
     * @param array  $meta
     * @param string $state
     *
     * @return mixed
     */
    public function setup(int $amount, array $meta = [], string $state = 'pending')
    {
        $subject_id   = $this->getPrimaryValue();
        $subject_type = __CLASS__;

        $class       = $this->getTransactionClass();
        $transaction = (new $class)
            ->fill(compact('amount', 'meta', 'state', 'subject_id', 'subject_type'));

        return $transaction;
    }

    /**
     * Get new transaction record in database.
     *
     * @param int    $amount
     * @param array  $meta
     * @param string $state
     *
     * @return Transaction
     */
    public function transaction(int $amount, array $meta = [], string $state = 'pending'): Transaction
    {
        $transaction = $this->setup(...func_get_args());

        if ($state == 'success') {
            $transaction->makeSuccessful();
        } else {
            $transaction->save();
        }

        return $transaction;
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
        if (! $field = $this->keepBalance()) {
            return true;
        }

        return $this->attributes[$field] >= abs($transaction->getAttributeFromArray('amount'));
    }

    /**
     * Recalculate subject's balance.
     *
     * @return mixed
     */
    public function recalculateBalance()
    {
        return $this->transactions()
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
        if (isset($this->primaryKey)) {
            return $this->{$this->primaryKey};
        }

        return false;
    }

    /**
     * Determine if subject need to keeping balance.
     *
     * @return bool|string
     */
    public function keepBalance()
    {
        if (isset($this->keepBalance)) {
            return $this->keepBalance;
        }

        return false;
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
