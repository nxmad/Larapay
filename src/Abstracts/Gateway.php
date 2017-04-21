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
     * Sign outcome request (insert request signature in request parameters).
     *
     * @param RepositoryContract $data
     *
     * @return string
     */
    abstract public function sign(RepositoryContract $data): string;

    /**
     * Get redirect url to payment gateway.
     *
     * @return string
     */
    abstract public function getRedirectUrl(): string;

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
     * Get redirect to payment gateway.
     *
     * @param Transaction $transaction
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function redirect(Transaction $transaction): RedirectResponse
    {
        $this->fill([
            'id'        => $transaction->getPrimaryValue(),
            'amount'    => $transaction->amount,
            'signature' => $this->sign($this->custom)
        ]);

        foreach ($this->required as $field) {
            if (! $this->custom->has($this->getAlias($field))) {
                throw new RuntimeException("Required field [$field] is not presented.");
            }
        }

        return RedirectResponse::create($this->getRedirectUrl() . '?' . http_build_query($this->custom->all()));
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
            $this->setCustom($key, $value);
        }

        return $this;
    }

    /**
     * Set custom field value respecting aliases.
     *
     * @param string $field
     * @param        $value
     *
     * @return self
     */
    public function setCustom(string $field, $value): self
    {
        $this->custom->set($this->getAlias($field), $value);

        return $this;
    }

    /**
     * Get custom field value respecting aliases.
     *
     * @param string  $field
     * @param mixed   $default
     *
     * @return mixed
     */
    public function getCustom(string $field, $default = null)
    {
        return $this->custom->get($this->getAlias($field), $default);
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
