<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use ReflectionClass;

trait VirtualAccountConfigValidate
{
    /**
     * @return VirtualAccountBuilder
     */
    protected function validateConfig()
    {
        static::validateType();

        $validators = [
            VerifyType::NONE_BASE => 'NoneBase',
            VerifyType::SINGLE_AMOUNT => 'SingleAmount',
            VerifyType::SINGLE_AMOUNT_DATE => 'SingleAmountAndDate',
        ];

        if (!array_key_exists(static::getConfig('type'), $validators)) {
            throw new InvalidArgumentException('Validator method not defined.');
        }

        $method = 'validate'.$validators[static::getConfig('type')];

        if (!method_exists($this, $method)) {
            throw new InvalidArgumentException('Validator method not implement.');
        }

        $this->{$method}();

        return $this;
    }

    /**
     * @return VirtualAccountBuilder
     */
    private function validateType()
    {
        if (!static::getConfig('type')) {
            throw new InvalidArgumentException('config attribute [type] not found.');
        }

        if (!in_array(static::getConfig('type'), (new ReflectionClass(VerifyType::class))->getConstants())) {
            throw new InvalidArgumentException('config attribute [type] not defined.');
        }

        return $this;
    }

    private function validateFields($fields = [])
    {
        Collection::make($fields)->each(function ($field, $index) {
            switch ($field['type']) {
                case 'string':
                    if (strlen($field['value']) > $field['max'] or strlen($field['value']) < $field['min']) {
                        throw new InvalidArgumentException(
                            strtr('config attribute [{field_name}] length error.', [
                                '{field_name}' => $index,
                            ])
                        );
                    }
                    break;
                case 'integer':
                    if (($field['value'] > $field['max']) or ($field['value'] < $field['min'])) {
                        throw new InvalidArgumentException(
                            strtr('config attribute [{field_name}] length error.', [
                                '{field_name}' => $index,
                            ])
                        );
                    }
                    break;
                case 'timestamp':
                    $dte = Carbon::createFromTimestamp($field['value']);
                    if (!$dte) {
                        throw new InvalidArgumentException(
                            strtr('config attribute [{field_name}] format error.', [
                                '{field_name}' => $index,
                            ])
                        );
                    }
                    break;
                default:
                    break;
            }
        });
    }

    /**
     * @return VirtualAccountBuilder
     */
    private function validateNoneBase()
    {
        static::validateFields([
            'number' => [
                'type' => 'string',
                'value' => static::getConfig('number'),
                'max' => 9,
                'min' => 9,
            ],
        ]);

        return $this;
    }

    /**
     * @return VirtualAccountBuilder
     */
    private function validateSingleAmount()
    {
        static::validateFields([
            'number' => [
                'type' => 'string',
                'value' => static::getConfig('number'),
                'max' => 8,
                'min' => 8,
            ],
            'amount' => [
                'type' => 'integer',
                'value' => static::getConfig('amount'),
                'max' => (1e+10 - 1),
                'min' => 1,
            ],
        ]);

        return $this;
    }

    /**
     * @return VirtualAccountBuilder
     */
    private function validateSingleAmountAndDate()
    {
        static::validateFields([
            'number' => [
                'type' => 'string',
                'value' => static::getConfig('number'),
                'max' => 4,
                'min' => 4,
            ],
            'amount' => [
                'type' => 'integer',
                'value' => static::getConfig('amount'),
                'max' => (1e+10 - 1),
                'min' => 1,
            ],
            'expired_at' => [
                'type' => 'timestamp',
                'value' => static::getConfig('expired_at'),
            ],
        ]);

        return $this;
    }
}
