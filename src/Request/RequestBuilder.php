<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Request;

use Closure;
use SoapFault;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\RequestCommonTrait as RequestCommon;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\RequestValidateTrait as RequestValidate;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\ResponseStateTrait as ResponseState;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\SoapRequestTrait as SoapRequest;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Response\RegisterResponse;

class RequestBuilder
{
    use SoapRequest, ResponseState, RequestValidate, RequestCommon;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->validateConfig($options)
            ->setCompany($options['company'])
            ->setWsdl($options['wsdl']);

        if (array_key_exists('cert', $options)) {
            $this->setCert($options['cert']);
        }
    }

    /**
     * @param array $params
     * @param Closure $errorHandler
     *
     * @return RequestBuilder
     */
    public function make(array $params, Closure $errorHandler = null)
    {
        return $this->validateParams($params)
            ->makeSoapRequest($params, $errorHandler);
    }

    /**
     * @param array $params
     * @param Closure $errorHandler
     *
     * @return RequestBuilder|Closure
     */
    protected function makeSoapRequest(array $params, Closure $errorHandler = null)
    {
        $this->client = $this->genClient(static::genSoapHeader());

        try {
            $res = $this->getClient()
                ->__soapCall('InstnCollPmtInstAdd', [
                    static::genSoapBody($params),
                ]);

        } catch (SoapFault $e) {
            $this->e = $e;

            return (! is_null($errorHandler)) ? $errorHandler($this) : $e;
        }

        return $this->response = new RegisterResponse($res);
    }
}
