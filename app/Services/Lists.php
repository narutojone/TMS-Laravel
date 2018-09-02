<?php

namespace App\Services;

use App\Lists\Contracts\Lists as ListsContract;

class Lists
{
    const NAMESPACE = '\App\Lists\\';
    const CONTRACT_METHOD = 'get';

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        $className = $this->parseClassName($method);

        if (! class_exists($className)) {
            throw new \InvalidArgumentException('Class ' . $className . ' does not exists.');
        }

        $list = new $className;

        if (! $list instanceof ListsContract) {
            throw new \Exception('Class ' . $className . ' should be instance of `' . ListsContract::class . '`.' );
        }

        return call_user_func_array([$list, self::CONTRACT_METHOD], $args);
    }

    /**
     * @param $className
     *
     * @return string
     */
    protected function parseClassName(string $className)
    {
        return  self::NAMESPACE . ucfirst($className);
    }
}
