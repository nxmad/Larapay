<?php

namespace Skylex\Larapay\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The state means user failed payment for Transaction.
     */
    const STATE_FAILED = 'failed';

    /**
     * The state means Transaction in pending.
     */
    const STATE_PENDING = 'pending';

    /**
     * The state means Transaction was canceled by user or admin.
     */
    const STATE_CANCELED = 'canceled';

    /**
     * The state means Transaction succeed.
     */
    const STATE_SUCCESSFUL = 'successful';

    /**
     * The allowed Transaction states.
     *
     * @var array
     */
    protected $allowedStates = [
        self::STATE_FAILED,
        self::STATE_PENDING,
        self::STATE_CANCELED,
        self::STATE_SUCCESSFUL,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['amount', 'meta', 'state', 'subject_id', 'subject_type'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'object'
    ];

    /**
     * Get amount of transaction respecting accuracy.
     *
     * @param $value
     *
     * @return int|float
     */
    public function getAmountAttribute($value)
    {
        return $value / pow(10, ($this->subject_type)::ACCURACY);
    }

    /**
     * Set amount of transaction respecting accuracy.
     *
     * @param int|float $value
     *
     * @return self
     */
    public function setAmountAttribute($value): self
    {
        $this->attributes['amount'] = $value * pow(10, ($this->subject_type)::ACCURACY);

        return $this;
    }

    /**
     * Get polymorphic relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
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
     * Get Transaction amount.
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get default payment description.
     * You have to override this method.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Payment';
    }

    /**
     * Update state, without respecting balance.
     *
     * @param string|null $state
     *
     * @return $this
     */
    public function __invoke(string $state = null)
    {
        if (! is_null($state)) {
            $this->state = $state;
        }

        if ($this->state == self::STATE_SUCCESSFUL) {
            $this->subject->increment(($this->subject_type)::KEEP, $this->getAttributeFromArray('amount'));
        }

        $this->save();

        return $this;
    }

    /**
     * Catch make* methods.
     * E.g. ->makeCanceled();
     *
     * @param string $method
     * @param array $parameters
     *
     * @return self|mixed
     */
    public function __call($method, $parameters)
    {
        if (strpos($method, 'make') !== false) {
            $state = mb_strtolower(str_replace('make', '', $method));

            if (in_array($state, $this->allowedStates)) {
                return $this($state);
            }
        }

        return parent::__call($method, $parameters);
    }
}
