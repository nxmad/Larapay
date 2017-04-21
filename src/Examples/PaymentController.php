<?php

namespace Skylex\Larapay\Examples;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Skylex\Larapay\Contracts\Payments;
use Skylex\Larapay\Traits\Transactions;

class PaymentController
{
    /**
     * The Payments instance. Define on construction and use anywhere!
     *
     * @var Payments
     */
    protected $payments;

    /**
     * PaymentController constructor.
     * Resolve by Dependency Injection.
     *
     * @param Payments $payments
     */
    public function __construct(Payments $payments)
    {
        $this->payments = $payments;
    }

    /**
     * Example of method to handle Subject's request.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function donate(Request $request)
    {
        $gateway = $this->payments->gateway($request->get('gateway'));

        /**
         * @var int          $amount
         * @var array        $meta
         * @var string       $state
         * @var Transactions $subject
         */
        $transaction = $subject->transaction($amount, $meta, $state);

        return $gateway->redirect($transaction);
    }

    /**
     * Example of method to handle payment gateway request.
     *
     * @route Route::any('callback/{gateway}', 'PaymentController@callback');
     *
     * @param $gateway
     * @param Request $request
     */
    public function callback($gateway, Request $request)
    {
        $gateway = $this->payments->gateway($gateway);

        if ($gateway->handle($request)) {
            // Some logic here. Request send from gateway's server.
        } else {
            // Something goes wrong. Request signature isn't approved.
        }
    }
}