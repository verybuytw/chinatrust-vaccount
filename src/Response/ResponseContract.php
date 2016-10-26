<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Response;

interface ResponseCode
{
    const TYPE_SUCCESS = '0000';
}

abstract class ResponseContract
{
    protected $result;

    public function __construct($res)
    {
        $this->setResult($res);
    }

    protected function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    protected function getResult()
    {
        return $this->result;
    }

    protected function getResponseCode()
    {
        return $this->getResult()->Status->StatusCode;
    }

    public function getMessage()
    {
        return $this->getResult()->Status->StatusDesc;
    }

    public function isSuccessful()
    {
        return ($this->getResponseCode() === ResponseCode::TYPE_SUCCESS);
    }
}
