<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Request;

use Illuminate\Support\Collection;
use InvalidArgumentException;

trait RequestValidateTrait
{
    /**
     * @param array $config
     *
     * @return mixed RequestBuilder
     */
    protected function validateConfig(array $config)
    {
        return $this->validateFromRequired(
            $config,
            ['wsdl', 'company']
        );
    }

    /**
     * @param array $params
     *
     * @return mixed RequestBuilder
     */
    protected function validateParams(array $params)
    {
        return $this->validateFromRequired(
            $params,
            ['channels', 'customer','vaccount', 'amount', 'expired_at']
        );
    }

    /**
     * @param array $params
     * @param array $required
     *
     * @return mixed RequestBuilder|InvalidArgumentException
     */
    protected function validateFromRequired(array $params, array $required = [])
    {
        Collection::make($required)->each(function ($value) use ($params) {
            if (! array_key_exists($value, $params)) {
                throw new InvalidArgumentException(strtr('{field} is required.', [
                    '{field}' => $value,
                ]));
            }
        });

        return $this;
    }
}
