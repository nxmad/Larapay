<?php

declare(strict_types=1);

namespace Nxmad\Larapay\Abstracts;

use Illuminate\Config\Repository;
use Nxmad\Larapay\Models\Transaction;
use Nxmad\Larapay\Requests\PaymentRequest;
use Nxmad\Larapay\Requests\CallbackRequest;
use Illuminate\Http\Request as HttpRequest;
use Nxmad\Larapay\Contracts\Gateway as GatewayContract;

abstract class Gateway implements GatewayContract
{
    /**
     * The list of gateway settings.
     *
     * @var Repository
     */
    protected $config;

    /**
     * The list of gateway requests.
     *
     * @var array
     */
    protected $requests = [
        'payment' => PaymentRequest::class,
        'callback' => CallbackRequest::class,
    ];

    /**
     * Sign request.
     *
     * @param Request $request
     *
     * @return string
     */
    abstract public function sign(Request $request): string;

    /**
     * Gateway constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = new Repository($config);
    }

    /**
     * Override gateway config values.
     *
     * @param array|string $key
     * @param mixed $value
     *
     * @return mixed
     */
    public function config($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->config($k, $v);
            }
        } elseif (! is_null($value)) {
            return $this->config->set($key, $value);
        }

        return $this->config->get($key);
    }

    /**
     * @param string $request
     * @return bool
     */
    public function hasRequest(string $request)
    {
        return array_key_exists($request, $this->requests);
    }

    /**
     * @param string $request
     * @return mixed|string
     */
    public function getRequest(string $request)
    {
        return $this->hasRequest($request) ? $this->requests[$request] : $request;
    }

    /**
     * @param array|string $name
     * @param null|Request $implementation
     *
     * @return self
     */
    public function addRequest($name, $implementation = null): self
    {
        if (is_array($name) && is_null($implementation)) {
            foreach ($name as $key => $value) {
                $this->addRequest($key, $value);
            }

            return $this;
        }

        $this->requests[$name] = $implementation;

        return $this;
    }

    /**
     * @param string $request
     * @param mixed $data
     *
     * @return mixed
     */
    public function call(string $request, $data = []): Request
    {
        $callable = $this->getRequest($request);

        /**
         * @var Request $request
         */
        $request = new $callable($this, $data);

        return $request;
    }

    /**
     * @param $transaction
     *
     * @return mixed
     */
    public function payment(Transaction $transaction)
    {
        return $this->call('payment', [
            Request::AMOUNT => $transaction->getAmount(),
            Request::ID => $transaction->getPrimaryValue(),
            Request::DESCRIPTION => $transaction->getDescription(),
        ]);
    }

    /**
     * @param HttpRequest $request
     *
     * @return Request
     */
    public function callback(HttpRequest $request)
    {
        return $this->call('callback', $request);
    }
}
