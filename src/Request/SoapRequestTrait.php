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
//            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
//            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'ssl_method' => SOAP_SSL_METHOD_TLS,
//            'stream_context' => stream_context_create([
//                'ssl' => [
//                    'verify_peer' => false,
//                    'verify_peer_name' => false,
//                    'allow_self_signed' => true,
//                ]
//             ])
        ];

        if (static::getCert()) {
            $options = array_merge($options, [
                'local_cert' => static::getCert(),
            ]);
        }

        dump($options);
//        exit;

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

        $expireAt = Carbon::createFromTimestamp($options->get('expired_at'));

        return [
            'TxnCode' => 'InstnCollPmtInstAdd',
            'RqUID' => static::genTransactionId(),
            'CollPmtInstInfo' => [
                'BillerInfo' => [
                    'IndustNum' => '53538135',
                    'BussType' => 81842,
                    'Name' => '非常科技',
                    'ContactInfo' => [
                        'PostAddr' => [
                            'Addr' => '台北市中正區'
                        ],
                        'Phone' => '02-3451123'
                    ],
                ],
                'BillInfo' => [
                    'CustPermId' => 'A123456789',
                    'BillingAcct' => '繳款人識別碼(MID)',
                    'Name' => '繳款人姓名',
                    'ContactInfo' => [
                        'PostAddr' => [
                            'Addr' => '台北市中正區',
                            'PostalCode' => '10466'
                        ],
                        'EmailAddr' => 'test@abc.com'
                    ],
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
