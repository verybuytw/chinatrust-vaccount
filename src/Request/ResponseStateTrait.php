<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Request;

trait ResponseStateTrait
{
    /**
     * @var array
     */
    protected $response;

    /**
     * @var mixed SoapFault|null
     */
    protected $e;
}
