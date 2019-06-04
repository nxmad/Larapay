<?php

declare(strict_types=1);

namespace Nxmad\Larapay\Traits;

use Illuminate\Config\Repository;

trait HasMutations
{
    /**
     * The original query of request.
     *
     * @var Repository
     */
    protected $query;

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
     * Magic set method.
     *
     * @param $name
     * @param $value
     *
     * @return self
     */
    public function __set($name, $value)
    {
        return self::set(...func_get_args());
    }

    /**
     * Get raw query.
     *
     * @return array
     */
    public function getRaw()
    {
        return $this->query->all();
    }

    /**
     * Set raw query.
     *
     * @param $query
     */
    public function setRaw($query)
    {
        $this->query = $query instanceof Repository ? $query : new Repository($query);
    }

    /**
     * Determine if request has a field.
     *
     * @param string $field
     *
     * @return bool
     */
    public function has(string $field): bool
    {
        return $this->get($field) !== null;
    }

    /**
     * Get mutated request field using accessor.
     *
     * @param string  $field
     * @param mixed   $default
     *
     * @return mixed
     */
    public function get(string $field, $default = null)
    {
        $accessorName = 'get' . $this->camelize($field);

        if (method_exists($this, $accessorName)) {
            return $this->{$accessorName}();
        }

        if (isset($this->aliases[$field])) {
            $field = $this->aliases[$field];
        }

        return $this->query->get($field, $default);
    }

    /**
     * Set request field.
     *
     * @param $field
     * @param $value
     *
     * @return self
     */
    public function set($field, $value = null)
    {
        if (is_array($field)) {
            return $this->fill($field);
        }

        $accessorName = 'set' . $this->camelize($field);

        if (method_exists($this, $accessorName)) {
            $value = $this->{$accessorName}($value);
        }

        if (isset($this->aliases[$field])) {
            $field = $this->aliases[$field];
        }

        $this->query->set($field, $value);

        return $this;
    }

    /**
     * @param mixed ...$fields
     */
    public function remove(...$fields)
    {
        foreach ($fields as $field)
        {
            if (is_array($field))
            {
                $this->remove(...$field);

                continue;
            }

            if (isset($this->aliases[$field])) {
                $field = $this->aliases[$field];
            }

            unset($this->query[$field]);
        }
    }

    /**
     * Fill request.
     *
     * @param array $parameters
     *
     * @return self
     */
    public function fill(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Get full mutated query.
     *
     * @param bool $object
     *
     * @return array|\stdClass
     */
    public function all($object = true)
    {
        $arr = $this->toArray();

        return $object ? (object) $arr : $arr;
    }

    /**
     * Request data to array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->query->all();
    }

    /**
     * Flat request.
     *
     * @param null $query
     * @param string $prefix
     *
     * @return array
     */
    public function flat($query = null, $prefix = '')
    {
        $result = [];

        if (is_null($query)) {
            $query = $this->query->all();
        }

        foreach ($query as $key => $value) {
            if (is_array($value) && $this->isAssoc($value)) {
                $result = array_merge($result, $this->flat($value, "{$prefix}{$key}."));
            } else {
                $result[$prefix . $key] = $value;
            }
        }

        return $result;
    }

    /**
     * Determine if array is associative.
     *
     * @param array $array
     *
     * @return bool
     */
    private function isAssoc(array $array)
    {
        if ([] === $array) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }
}
