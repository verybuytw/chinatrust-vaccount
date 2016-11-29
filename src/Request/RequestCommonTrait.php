<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Request;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

trait RequestCommonTrait
{
    /**
     * @var Collection
     */
    protected $company;

    /**
     * @param array $company
     *
     * @return RequestBuilder
     */
    protected function setCompany(array $company)
    {
        $this->company = Collection::make($company);

        return $this;
    }

    /**
     * @return Collection
     */
    protected function getCompany()
    {
        return $this->company;
    }

    /**
     * @return string
     */
    protected function genTransactionId()
    {
        return $this->getCompany()->get('id').sprintf('%015d', Carbon::now()->format('YmdHis'));
    }

    /**
     * @return array
     */
    protected function genChannelParams($method, Collection $options)
    {
        $conds = [
            static::CHANNEL_POST => function ($vaccount, $options) {
                return [
                    'SettlementMethod' => 'PostOffice',
                    'RefInfo' => [
                        'RefType' => 'PostBarcode2',
                        'RefId' => $vaccount,
                    ],
                ];
            },
            static::CHANNEL_BANK => function ($vaccount, $options) {
                return [
                    'SettlementMethod' => 'Bank',
                    'RefInfo' => [
                        [
                            'RefType' => 'BankBarcode1',
                            'RefId' => $vaccount,
                        ],
                        [
                            'RefType' => 'BankBarcode2',
                            'RefId' => $options->get('amount'),
                        ],
                    ],
                ];
            },
            static::CHANNEL_STORE => function ($vaccount, $options) {

                $memo = [
                    'field_name' => '非常勸敗',
                    'field_value' => '購物消費選項',
                ];

                if ($options->has('store')) {
                    $memo = $options->get('store');
                }

                return [
                    'SettlementMethod' => 'StoreAgent',
                    'RefInfo' => [
                        'RefType' => 'StoreBarcode2',
                        'RefId' => $vaccount,
                    ],
                    'Memo' => sprintf('%- 50s%- 50s', $memo['field_value'], $memo['field_name']),
                ];
            },
        ];

        if (! array_key_exists($method, $conds)) {
            throw new InvalidArgumentException('Undefined arguments.');
        }

        return $conds[$method]($options->get('vaccount'), $options);
    }
}
