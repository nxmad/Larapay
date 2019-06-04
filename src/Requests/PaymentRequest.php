<?php

declare(strict_types=1);

namespace Nxmad\Larapay\Requests;

use Nxmad\Larapay\Units\Redirect;
use Nxmad\Larapay\Abstracts\Request;

class PaymentRequest extends Request
{
    /**
     * @return Redirect
     */
    public function send()
    {
        parent::send();

        if ($this->gateway->config('public')) {
            $this->set(Request::PUBLIC, $this->gateway->config('public'));
        }

        return $this->redirect();
    }
}
