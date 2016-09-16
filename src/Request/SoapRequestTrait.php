<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Request;

use SoapClient;
use SoapHeader;

trait SoapRequestTrait
{
    /**
     * @var Path
     */
    protected $wsdl;

    /**
     * @var SoapClient
     */
    protected $client;

    /**
     * @return string
     */
    protected function getWsdl()
    {
        return $this->wsdl;
    }

    /**
     * @param string $wsdl
     *
     * @return RequestBuilder
     */
    protected function setWsdl($wsdl)
    {
        $this->wsdl = $wsdl;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return SoapHeader
     */
    public function genSoapHeader(array $options)
    {

//        $headers = [
//            'from' => ['name' => $companyName], // setting from chinatrust. default: VERYBUY
//            'to' => ['name' => 'CTCB'],
//            'operationID' => 'InstnCollPmt/1.0/InstnCollPmtInstAdd',
//            'operationType' => 'syncRequestResponse',
//            'transactionID' => $serialNumber, // length 20
//        ];

        return new SoapHeader(
            'http://www.tibco.com/namespaces/bc/2002/04/partyinfo.xsd',
            'PartyInfo',
            $options,
            false
        );
    }

    /**
     * @return SoapClient
     */
    protected function genClient(SoapHeader $header = null)
    {
        $wsdl = static::getWsdl();

        $client = new SoapClient($wsdl, [
            'encoding' => 'UTF-8',
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => 1,
        ]);

        if (! is_null($header)) {
            $client->__setSoapHeaders($header);
        }

        return $client;
    }

    /**
     * @return SoapClient
     */
    protected function getClient()
    {
        return $this->client;
    }
}
