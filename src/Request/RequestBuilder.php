<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Request;

use Carbon\Carbon;
use Closure;
use SoapFault;
use InvalidArgumentException;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\RequestValidateTrait as RequestValidate;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\ResponseStateTrait as ResponseState;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\SoapRequestTrait as SoapRequest;

class RequestBuilder
{
    use SoapRequest, ResponseState, RequestValidate;

    /**
     * @var int Company Id
     */
    protected $companyId;

    /**
     * @var string Company short name
     */
    protected $companyName;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->validateConfig($options)
            ->setCompanyId($options['id'])
            ->setCompanyName($options['name'])
            ->setWsdl($options['wsdl']);

        if (array_key_exists('cert', $options)) {
            $this->setCert($options['cert']);
        }
    }

    /**
     * @param int $companyId
     *
     * @return RequestBuilder
     */
    protected function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * @return int
     */
    protected function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param string $companyName
     *
     * @return RequestBuilder
     */
    protected function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * @return string
     */
    protected function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @return string
     */
    public function genTransactionId()
    {
        return $this->getCompanyId().sprintf('%015d', Carbon::now()->format('YmdHis'));
    }

    /**
     * @param array $params
     * @param Closure $errorHandler
     *
     * @return RequestBuilder|InvalidArgumentException
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
            $this->response = $this->getClient()
                ->__soapCall('InstnCollPmtInstAdd', [
                    static::genSoapBody($params),
                ]);
        } catch (SoapFault $e) {
            $this->e = $e;

            (! is_null($errorHandler)) ? $errorHandler($this) : null;
        }

        return $this;
    }
}
