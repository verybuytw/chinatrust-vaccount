<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Request;

use Carbon\Carbon;
use Closure;
use SoapFault;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\ResponseStateTrait as ResponseState;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\SoapRequestTrait as SoapRequest;

class RequestBuilder
{
    use SoapRequest, ResponseState;

    /**
     * @param string $wsdl          file path
     * @param int $companyId        company id
     * @param string $companyName   company short name
     */
    public function __construct($wsdl, $companyId, $companyName)
    {
        $this->setCompanyId($companyId)
            ->setCompanyName($companyName)
            ->setWsdl($wsdl);
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

    public function genTransactionId()
    {
        return $this->getCompanyId().sprintf('%015d', Carbon::now()->format('YmdHis'));
    }

    public function make($params, Closure $errorHandler = null)
    {
        $this->client = $this->genClient(
            static::genSoapHeader([
                'from' => ['name' => static::getCompanyName()],
                'to' => ['name' => 'CTCB'],
                'operationID' => 'InstnCollPmt/1.0/InstnCollPmtInstAdd',
                'operationType' => 'syncRequestResponse',
                'transactionID' => static::genTransactionId(),
            ])
        );

        try {
            $this->response = $this->getClient()
                ->__soapCall('InstnCollPmtInstAdd', [$params]);
        } catch (SoapFault $e) {
            $this->e = $e;

            (! is_null($errorHandler)) ? $errorHandler($this) : null;
        }

        return $this;
    }

    public function debug()
    {
        return [
            $this->response,
            $this->client,
            $this->e,
        ];
    }
}
