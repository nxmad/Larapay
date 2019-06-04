<?php

declare(strict_types=1);

namespace Nxmad\Larapay\Abstracts;

use Illuminate\Config\Repository;
use Nxmad\Larapay\Units\Redirect;
use Nxmad\Larapay\Contracts\Fields;
use Nxmad\Larapay\Traits\HasMutations;
use Illuminate\Http\Request as HttpRequest;

abstract class Request implements Fields
{
    use HasMutations;

    const GET = 'GET';
    const POST = 'POST';
    const CUSTOM = '_';

    /**
     * The gateway instance.
     *
     * @var Gateway
     */
    protected $gateway;

    /**
     * Original HTTP request if presented.
     *
     * @var null|HttpRequest
     */
    protected $http = null;

    /**
     * The list of required fields by request.
     *
     * @var array
     */
    protected $required = [];

    /**
     * The list of fields to transform.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * The HTTP method of request.
     *
     * @var string
     */
    protected $method = Redirect::GET;

    /**
     * Request constructor.
     *
     * @param Gateway $gateway
     * @param array|HttpRequest $data
     */
    public function __construct(Gateway $gateway, $data = [])
    {
        $this->gateway = $gateway;
        $this->query = new Repository;

        if (is_array($data)) {
            $this->fill($data);
        }

        if ($data instanceof HttpRequest)
        {
            $this->http = $data;

            $this->setRaw($data->all());
        }
    }

    /**
     * Get original http request.
     *
     * @return HttpRequest
     */
    public function getHttpRequest()
    {
        return $this->http;
    }

    /**
     * You can override endpoint method.
     *
     * @return mixed
     */
    public function endpoint()
    {
        return $this->gateway->getEndpoint($this);
    }

    /**
     * You can override sign method.
     *
     * @return string
     */
    public function sign()
    {
        return $this->gateway->sign($this);
    }

    /**
     * Transform request to redirect.
     *
     * @return Redirect
     */
    public function redirect()
    {
        return new Redirect($this->endpoint(), $this->method, $this->all());
    }

    /**
     * Fires before send.
     */
    public function prepare()
    {
        // ...
    }

    /**
     * @return void
     */
    public function send()
    {
        if (in_array(self::SIGNATURE, $this->required)) {
            $this->set(self::SIGNATURE, $this->sign());
        }

        foreach ($this->required as $field) {
            if (! $this->has($field)) {
                throw new \RuntimeException("Required field [{$field}] is not presented.");
            }
        }

        $this->prepare();
    }

    /**
     * Convert no_one_should_use_this_case to CamelCase.
     *
     * @param string $str
     * @param string $sep
     *
     * @return string
     */
    protected function camelize(string $str, string $sep = '_'): string
    {
        return str_replace($sep, '', ucwords($str, $sep));
    }
}
