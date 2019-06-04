<?php

declare(strict_types=1);

namespace Nxmad\Larapay\Requests;

use Nxmad\Larapay\Abstracts\Gateway;
use Nxmad\Larapay\Abstracts\Request;
use Nxmad\Larapay\Exceptions\SignatureValidateException;

class CallbackRequest extends Request
{
    /**
     * CallbackRequest constructor.
     *
     * @param Gateway $gateway
     * @param array $data
     *
     * @throws SignatureValidateException
     */
    public function __construct(Gateway $gateway, $data = [])
    {
        parent::__construct($gateway, $data);

        if (! $this->validate()) {
            throw new SignatureValidateException;
        }
    }

    /**
     * @return bool
     */
    protected function validate(): bool
    {
        return $this->get(self::SIGNATURE) === $this->sign();
    }
}
