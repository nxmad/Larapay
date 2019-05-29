<?php

declare(strict_types=1);

namespace Nxmad\Larapay\Abstracts;

use Nxmad\Larapay\Units\Redirect;
use Illuminate\Config\Repository;
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
     * The HTTP request instance if presented.
     *
     * @var HttpRequest|null
     */
    protected $request;

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
     * @param HttpRequest|null $request
     */
    public function __construct(Gateway $gateway, ?HttpRequest $request = null)
    {
        $this->gateway = $gateway;
        $this->request = $request;
        $this->query = new Repository;

        if ($request) {
            $this->fill($request->all());
        }
    }

    /**
     * Get original query before mutation.
     *
     * @return Repository
     */
    public function getOriginal()
    {
        return $this->query;
    }

    /**
     * Get endpoint for current request.
     *
     * @return mixed
     */
    public function endpoint()
    {
        return $this->gateway->getEndpoint($this);
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
            $this->set(self::SIGNATURE, $this->gateway->sign($this));
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
