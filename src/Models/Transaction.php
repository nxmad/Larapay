<?php

namespace Skylex\Larapay\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['amount', 'subject', 'state', 'subject_id', 'subject_type'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'object'
    ];

    /**
     * The accuracy for conversions float to integer when saving to database.
     * 0 if you don't need any conversions.
     *
     * @var int
     */
    protected $accuracy = 2;

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
     * Get amount of transaction respecting accuracy.
     *
     * @param $value
     *
     * @return int|float
     */
    public function getAmountAttribute($value)
    {
        return $value / pow(10, $this->accuracy);
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
        $this->attributes['amount'] = $value * pow(10, $this->accuracy);

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
     * Mark transaction as successful.
     */
    public function makeSuccessful()
    {
        $this->state = 'successful';
        $this->save();

        $keeping = $this->subject->keepBalance();

        if ($keeping) {
            $this->subject->increment($keeping, $this->attributes['amount']);
        }
    }

    /**
     * Mark transaction as failed.
     */
    public function makeFailed()
    {
        $this->state = 'failed';
        $this->save();
    }

    /**
     * Mark transaction as canceled.
     */
    public function makeCanceled()
    {
        $this->state = 'canceled';
        $this->save();
    }
}
