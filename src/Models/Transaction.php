<?php

declare(strict_types=1);

namespace Nxmad\Larapay\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The state means user failed payment for Transaction.
     */
    const FAILED = 'failed';

    /**
     * The state means Transaction in pending.
     */
    const PENDING = 'pending';

    /**
     * The state means Transaction was canceled by user or admin.
     */
    const CANCELED = 'canceled';

    /**
     * The state means Transaction succeed.
     */
    const SUCCESSFUL = 'successful';

    /**
     * The list of allowed Transaction states.
     *
     * @var array
     */
    const STATES = [
        self::FAILED,
        self::PENDING,
        self::CANCELED,
        self::SUCCESSFUL,
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
     * Determine if Subjects's balance is enough for transaction.
     *
     * @return bool
     */
    public function affordable(): bool
    {
        return $this->subject->canAfford($this);
    }

    /**
     * Set subject of transaction.
     *
     * @param $instance
     *
     * @return self
     */
    public function setSubject($instance): self
    {
        $this->subject_id = $instance->id;
        $this->subject_type = get_class($instance);

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
     * Invoke() to change the state.
     *
     * @param string|null $state
     *
     * @return $this
     */
    public function __invoke(string $state = null)
    {
        if ($this->exists && $this->state === $state) {
            return $this;
        }

        if (! is_null($state)) {
            $this->state = $state;
        }

        if ($this->state === self::SUCCESSFUL && defined($this->subject_type . '::KEEP')) {
            $this->subject()->increment(($this->subject_type)::KEEP, $this->amount);
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

            if (in_array($state, self::STATES)) {
                return $this($state);
            }
        }

        return parent::__call($method, $parameters);
    }
}
