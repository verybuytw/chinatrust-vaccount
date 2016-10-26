<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Response;

abstract class ResponseContract
{
    protected $result;

    /**
     * @param object $res
     */
    public function __construct($res)
    {
        $this->setResult($res);
    }

    /**
     * @param object $result
     *
     * @return self
     */
    protected function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return object
     */
    protected function getResult()
    {
        return $this->result;
    }

    /**
     * @return string
     */
    protected function getResponseCode()
    {
        return $this->getResult()->Status->StatusCode;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->getResult()->Status->StatusDesc;
    }

    /**
     * @return boolean
     */
    public function isSuccessful()
    {
        return ($this->getResponseCode() === ResponseCode::TYPE_SUCCESS);
    }
}
