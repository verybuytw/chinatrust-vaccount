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
            'encoding' => 'UTF-8',
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => 1,
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
                'from' => ['name' => static::getCompanyName()], // setting from chinatrust. default: VERYBUY
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

        $field_name = '非常勸敗';
        $field_value = '購物消費選項';

        if ($options->has('store')) {
            $store = Collection::make($options->get('store'));
            if ($store->has('field_name')) {
                $field_name = $store->get('field_name');
            }
            if ($store->has('field_value')) {
                $field_value = $store->get('field_value');
            }
        }

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
                                'RefId' => $options->get('vaccount'),
                            ],
                        ],
                        [
                            'SettlementMethod' => 'StoreAgent',
                            'RefInfo' => [
                                'RefType' => 'StoreBarcode2',
                                'RefId' => $options->get('vaccount'),
                            ],
                            'Memo' => sprintf('%- 50s%- 50s', $field_value, $field_name),
                        ],
                    ],
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
