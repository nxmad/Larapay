<?php

namespace Skylex\Larapay\Abstracts;

use RuntimeException;
use Illuminate\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Skylex\Larapay\Models\Transaction;
use Skylex\Larapay\Contracts\Gateway as GatewayContract;
use Illuminate\Contracts\Config\Repository as RepositoryContract;

abstract class Gateway implements GatewayContract
{
    /**
     * The list of aliases for payment gateway.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * The list of required request attributes.
     *
     * @var array
     */
    protected $required = [];

    /**
     * The general settings for payment processor, like secret key or merchant id.
     *
     * @var Repository
     */
    protected $config;

    /**
     * The temporary settings for payment processor, actual only in for this request, like payment description, etc.
     *
     * @var Repository
     */
    protected $custom;

    /**
     * Payment process method.
     * Possible values are below.
     *
     * @var int
     */
    protected $method = self::LARAPAY_GET_REDIRECT;

    /**
     * Classic redirection with GET parameters.
     */
    const LARAPAY_GET_REDIRECT = 0;

    /**
     * Redirect with POST data for old gateways (using hack with form).
     */
    const LARAPAY_POST_REDIRECT = 1;

    /**
     * No redirect method (e.g. for card payments)
     */
    const LARAPAY_NO_REDIRECT = 2;

    /**
     * Sign outcome request (insert request signature in request parameters).
     *
     * @param array $data
     *
     * @return string
     */
    abstract public function sign(array $data): string;

    /**
     * Custom gateway logic instead redirect.
     *
     * @return mixed
     */
    abstract public function noRedirect();

    /**
     * Gateway constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->custom = new Repository;
        $this->config = new Repository($config);
    }

    /**
     * Prepare redirect, set basic data and sign it.
     *
     * @param Transaction $transaction
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public function prepare(Transaction $transaction): self
    {
        $this->fill([
            'amount'      => $transaction->getAmount(),
            'description' => $transaction->getDescription(),
            'id'          => $transaction->getPrimaryValue(),
        ]);

        $this->signature = $this->sign($this->custom->all());

        foreach ($this->required as $field) {
            if (! $this->custom->has($this->getAlias($field))) {
                throw new RuntimeException("Required field [$field] is not presented.");
            }
        }

        return $this;
    }

    /**
     * Process payment.
     *
     * @param Transaction $transaction
     * @param int $method
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function process(Transaction $transaction, $method = self::LARAPAY_GET_REDIRECT)
    {
        $this->prepare($transaction);
        $method = func_num_args() == 2 ? $method : $this->method;

        if ($method == self::LARAPAY_NO_REDIRECT) {
            return $this->noRedirect();
        }

        return view('larapay::form', [
            ''
        ]);
    }

    /**
     * Fill custom parameters.
     *
     * @param array $parameters
     *
     * @return Gateway
     */
    public function fill(array $parameters): self
    {
        foreach ($parameters as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Set custom field value respecting aliases.
     *
     * @param $field
     * @param $value
     *
     * @return self
     */
    public function set($field, $value = null): self
    {
        if (is_array($field)) {
            return $this->fill($field);
        }

        $this->custom->set($this->getAlias($field), $value);

        return $this;
    }

    /**
     * Magic set method.
     *
     * @param $name
     * @param $value
     *
     * @return Gateway
     */
    public function __set($name, $value)
    {
        return self::set(...func_get_args());
    }

    /**
     * Get custom field value respecting aliases.
     *
     * @param string  $field
     * @param mixed   $default
     *
     * @return mixed
     */
    public function get(string $field, $default = null)
    {
        return $this->custom->get($this->getAlias($field), $default);
    }

    /**
     * Magic get method.
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return self::get(...func_get_args());
    }

    /**
     * Get slug of gateway based on class name.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return str_slug(array_last(explode('\\', __CLASS__)));
    }

    /**
     * Determine if field has alias for payment gateway.
     * E.g., some gateways can accept description in $_GET parameter vendorPrefix_desc,
     * so we need conversion: 'description' => 'vendorPrefix_desc'.
     *
     * @param string $field
     *
     * @return string
     */
    protected function getAlias(string $field): string
    {
        if (isset($this->aliases[$field])) {
            return $this->aliases[$field];
        }

        return $field;
    }
}
