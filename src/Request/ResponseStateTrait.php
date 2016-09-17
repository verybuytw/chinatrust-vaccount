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

    /**
     * @return boolean
     */
    public function isSuccessFul()
    {
        return is_null($this->e) and ($this->response->Status->StatusCode == '0000');
    }
}
