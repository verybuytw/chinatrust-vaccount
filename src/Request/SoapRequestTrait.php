<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Request;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use SoapClient;
use SoapHeader;

trait SoapRequestTrait
{
    /**
     * @var string  wsdl file path
     */
    protected $wsdl;

    /**
     * @var string cert file path
     */
    protected $cert;

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
     * @return string
     */
    protected function getCert()
    {
        return $this->cert;
    }

    /**
     * @param string $cert
     *
     * @return RequestBuilder
     */
    protected function setCert($cert)
    {
        $this->cert = $cert;

        return $this;
    }

    /**
     * @return SoapClient
     */
    protected function genClient(SoapHeader $header = null)
    {
        $wsdl = static::getWsdl();

        $options = [
            'soap_version' => SOAP_1_2,
            'encoding' => 'UTF-8',
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => 1,
            'exceptions' => 1,
            'ssl_method' => SOAP_SSL_METHOD_TLS,
            'stream_context' => stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                ],
                'https' => [
                    'curl_verify_ssl_peer'  => false,
                    'curl_verify_ssl_host'  => false
                 ],
             ])
        ];

        if (static::getCert()) {
            $options = array_merge($options, [
                'local_cert' => static::getCert(),
            ]);
        }

        $client = new SoapClient($wsdl, $options);

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

    /**
     * @return SoapHeader
     */
    public function genSoapHeader()
    {
        return new SoapHeader(
            'http://www.tibco.com/namespaces/bc/2002/04/partyinfo.xsd',
            'PartyInfo',
            [
                'from' => ['name' => static::getCompany()->get('name')], // setting from chinatrust. default: VERYBUY
                'to' => ['name' => 'CTCB'],
                'operationID' => 'InstnCollPmt/1.0/InstnCollPmtInstAdd',
                'operationType' => 'syncRequestResponse',
                'transactionID' => static::genTransactionId(), // length 20
            ],
            false
        );
    }

    /**
     * @param array $options
     * @return array
     */
    public function genSoapBody(array $options)
    {
        $options = Collection::make($options);
        $customer = Collection::make($options->get('customer'));

        $expireAt = Carbon::createFromTimestamp($options->get('expired_at'));

        return [
            'TxnCode' => 'InstnCollPmtInstAdd',
            'RqUID' => static::genTransactionId(),
            'CollPmtInstInfo' => [
                'BillerInfo' => [
                    'IndustNum' => static::getCompany()->get('number'),
                    'BussType' => static::getCompany()->get('id'),
                    'Name' => static::getCompany()->get('alias'),
                    'ContactInfo' => '',
                ],
                'BillInfo' => [
                    'CustPermId' => '',
                    'BillingAcct' => $customer->get('mid'),
                    'Name' =>  $customer->get('name'),
                    'ContactInfo' => '',
                    'BillDt' => $expireAt->format('Y-m-d'),
                    'DueDt' => $expireAt->format('Y-m-d'),
                    'SettlementInfo' => Collection::make($options->get('channels'))->map(function($method) use ($options) {
                        return static::genChannelParams($method, $options);
                    })->all(),
                    'BillSummAmt' => [
                        'CurAmt' => [
                            'Amt' => $options->get('amount'),
                        ],
                    ],
                ],
            ],
        ];
    }
}
