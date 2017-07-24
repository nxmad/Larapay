<?php

namespace Skylex\Larapay\Contracts;

use Illuminate\Http\Request;
use Skylex\Larapay\Models\Transaction;

interface Gateway
{
    /**
     * Sign outcome request data.
     *
     * @param array $data
     *
     * @return string
     */
    public function sign(array $data): string;

    /**
     * Process payment.
     *
     * @param Transaction $transaction
     *
     * @return mixed
     */
    public function interact(Transaction $transaction);

    /**
     * Determine if request was sent originally from payment gateway.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function handle(Request $request): bool;

    /**
     * Get gateway's interaction address.
     *
     * @return string
     */
    public function getInteractionUrl(): string;
}
