<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Request;

use Carbon\Carbon;
use SoapFault;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\ResponseStateTrait as ResponseState;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\SoapRequestTrait as SoapRequest;

class RequestBuilder
{
    const COMPANY_NAME = 'VERYBUY';

    use SoapRequest, ResponseState;

    /**
     * @param int $companyId
     * @param string $wsdl  File Path
     */
    public function __construct($companyId, $wsdl)
    {
        $this->setCompanyId($companyId)
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
     * @return string
     */
    protected function getCompanyId()
    {
        return $this->companyId;
    }

    public function genTransactionId()
    {
        return $this->getCompanyId().sprintf('%015d', Carbon::now()->format('YmdHis'));
    }

    public function make(array $header, $body)
    {
        $this->client = $this->genClient(
            static::genSoapHeader($header)
        );

//        try {
            $this->response = $this->getClient()
                ->__soapCall('InstnCollPmtInstAdd', [$body]);
//        } catch (SoapFault $e) {
//            $this->e = $e;
//        }

        return $this;
    }
}
