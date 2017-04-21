<?php

namespace Skylex\Larapay\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Skylex\Larapay\Models\Transaction;
use Illuminate\Contracts\Config\Repository;

interface Gateway
{
    /**
     * Sign outcome request data.
     *
     * @param Repository $data
     *
     * @return string
     */
    public function sign(Repository $data): string;

    /**
     * Determine if request was sent originally from payment gateway.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function handle(Request $request): bool;

    /**
     * Get redirect url to payment gateway.
     *
     * @return string
     */
    public function getRedirectUrl(): string;

    /**
     * Return redirect to payment processor.
     *
     * @param Transaction $transaction
     *
     * @return RedirectResponse
     */
    public function redirect(Transaction $transaction): RedirectResponse;
}
