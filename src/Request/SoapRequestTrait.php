<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Request;

use Carbon\Carbon;
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
     * @param array $options
     * @return array
     */
    public function genSoapBody(array $options)
    {
        $expireAt = Carbon::createFromTimestamp($options['expired_at']);

        return [
            'TxnCode' => 'InstnCollPmtInstAdd',
            'RqUID' => static::genTransactionId(),
            'CollPmtInstInfo' => [
                'BillerInfo' => [
                    'IndustNum' => '03077208',
                    'BussType' => '07914',
                    'Name' => '非常科技',
                    'ContactInfo' => '',
                ],
                'BillInfo' => [
                    'CustPermId' => '',
                    'BillingAcct' => '繳款人識別碼(MID)',
                    'Name' => '繳款人姓名',
                    'ContactInfo' => '',
                    'BillDt' => $expireAt->format('Y-m-d'),
                    'DueDt' => $expireAt->format('Y-m-d'),
                    'SettlementInfo' => [
                        [
                            'SettlementMethod' => 'Bank',
                            'RefInfo' => [
                                'RefType' => 'BankBarcode2',
                                'RefId' => $options['vaccount'],
                            ],
                        ],
                        [
                            'SettlementMethod' => 'StoreAgent',
                            'RefInfo' => [
                                'RefType' => 'StoreBarcode2',
                                'RefId' => $options['vaccount'],
                            ],
                            'Memo' => sprintf('%- 50s%- 50s', $options['field_value'], $options['field_name']),
                        ],
                    ],
                    'BillSummAmt' => [
                        'CurAmt' => [
                            'Amt' => $options['amount'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
