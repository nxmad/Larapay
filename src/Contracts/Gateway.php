<?php

namespace Nxmad\Larapay\Contracts;

use Nxmad\Larapay\Abstracts\Request;

interface Gateway
{
    /**
     * Sign request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function sign(Request $request);

    /**
     * Get endpoint for request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getEndpoint(Request $request);
}
