<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Request;

use Illuminate\Support\Collection;
use InvalidArgumentException;

trait RequestValidateTrait
{
    /**
     * @param array $params
     *
     * @return RequestBuilder|InvalidArgumentException
     */
    protected function validateParams(array $params)
    {
        Collection::make(['vaccount', 'amount', 'expired_at'])->each(function($value) use($params) {
            if (!array_key_exists($value, $params)) {
                throw new InvalidArgumentException(strtr('{field} is required.', [
                    '{field}' => $value
                ]));
            }
        });

        return $this;
    }
}
