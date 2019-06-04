<?php

declare(strict_types=1);

namespace Nxmad\Larapay\Units;

class Redirect
{
    const GET = 'GET';
    const POST = 'POST';

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $query = [];

    /**
     * Redirect constructor.
     *
     * @param $url
     * @param string $method
     * @param array $query
     */
    public function __construct($url, $method = self::GET, $query = [])
    {
        $this->url = $url;
        $this->query = $query;
        $this->method = $method;
    }

    /**
     * Convert to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Convert to JSON.
     *
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->raw());
    }

    /**
     * Convert to response.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function toResponse()
    {
        if ($this->method === self::GET) {
            return redirect($this->raw()->url);
        }

        return view('larapay::form', [
            'method' => 'POST',
            'action' => $this->url,
            'data'   => $this->query,
        ]);
    }

    /**
     * Get redirect summary.
     *
     * @return object
     */
    public function raw()
    {
        $url = $this->url;

        if ($this->method === self::GET) {
            $url = $this->url . '?' . http_build_query($this->query);
        }

        return (object) [
            'url' => $url,
            'query' => $this->query,
            'method' => $this->method,
            'url_original' => $this->url,
        ];
    }
}
