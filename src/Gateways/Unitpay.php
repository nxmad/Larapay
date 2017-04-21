<?php

namespace Skylex\Larapay\Gateways;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Skylex\Larapay\Abstracts\Gateway;
use Illuminate\Contracts\Config\Repository;
use Skylex\Larapay\Contracts\Gateway as Contract;

class Unitpay extends Gateway implements Contract
{
    /**
     * The list of aliases for payment gateway.
     *
     * @var array
     */
    public $aliases = [
        'id'          => 'account',
        'amount'      => 'sum',
        'description' => 'desc',
    ];

    /**
     * The list of required request parameters.
     *
     * @var array
     */
    protected $required = [
        'id',
        'amount',
        'signature',
        'description',
    ];

    /**
     * Get redirect url to payment gateway.
     *
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return 'https://unitpay.ru/pay/' . $this->config->get('public');
    }

    /**
     * Sign outcome request (insert request signature in request parameters)
     *
     * @param Repository $data
     *
     * @return string
     */
    public function sign(Repository $data): string
    {
        $data = Arr::sortRecursive($data->all());
        $withSecret = array_add($data, 'secret', $this->config->get('secret'));

        return hash($this->config->get('algo'), join('{up}', $withSecret));
    }

    /**
     * Determine if request was sent originally from payment gateway.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function handle(Request $request): bool
    {
        list($method, $params) = array_values($request->all());

        $incomeSignature = $params['signature'];
        $withoutSignature = Arr::except($params, ['sign', 'signature']);

        array_push($withoutSignature, $this->config->get('secret'));
        array_unshift($withoutSignature, $method);

        $signature = hash($this->config->get('algo'), join('{up}', $withoutSignature));

        return $incomeSignature == $signature;
    }
}
