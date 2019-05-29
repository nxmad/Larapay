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
     * The mutated query of request.
     *
     * @var Repository
     */
    protected $mutated;

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
     * Determine if request has a field.
     *
     * @param string $field
     *
     * @return bool
     */
    public function has(string $field): bool
    {
        return $this->mutated->has($field);
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

        return $this->mutated->get($field, $default);
    }

    /**
     * Set request field and then mutate.
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

        $mutatorName = 'set' . $this->camelize($field);

        if (method_exists($this, $mutatorName)) {
            $result = $this->{$mutatorName}($value);

            $this->queryHasChanged();

            return $result;
        }

        $this->query->set($field, $value);

        $this->queryHasChanged();

        return $this;
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

        $this->queryHasChanged();

        return $this;
    }

    /**
     * Get full mutated query.
     *
     * @return array
     */
    public function all()
    {
        return $this->mutated->all();
    }

    /**
     * Update mutated fields.
     */
    protected function queryHasChanged()
    {
        $this->mutated = $this->mutate();
    }

    /**
     * Get mutated query.
     *
     * @return Repository
     */
    public function mutate()
    {
        $result = [];
        $aliases = $this->aliases;

        foreach ($this->flat() as $key => $value) {
            if (array_key_exists($key, $aliases)) {
                $key = $aliases[$key];
            }

            $result[$key] = $value;
        }

        return new Repository($result);
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
            if (is_array($value)) {
                $result = array_merge($result, $this->flat($value, "{$prefix}{$key}."));
            } else {
                $result[$prefix . $key] = $value;
            }
        }

        return $result;
    }
}
