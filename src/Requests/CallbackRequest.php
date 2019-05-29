<?php

declare(strict_types=1);

namespace Nxmad\Larapay\Requests;

use Nxmad\Larapay\Abstracts\Gateway;
use Nxmad\Larapay\Abstracts\Request;
use Illuminate\Http\Request as HttpRequest;
use Nxmad\Larapay\Exceptions\SignatureValidateException;

class CallbackRequest extends Request
{
    /**
     * CallbackRequest constructor.
     *
     * @param Gateway $gateway
     * @param HttpRequest $request
     *
     * @throws SignatureValidateException
     */
    public function __construct(Gateway $gateway, HttpRequest $request)
    {
        parent::__construct($gateway, $request);

        if (! $this->validate()) {
            throw new SignatureValidateException;
        }
    }

    /**
     * @return bool
     */
    protected function validate(): bool
    {
        return $this->get(self::SIGNATURE) === $this->gateway->sign($this);
    }
}
