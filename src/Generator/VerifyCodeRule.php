<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Generator;

trait VerifyCodeRule
{
    /**
     * @param array      $rule
     * @param string|int $number
     *
     * @return int
     */
    protected function genVerifyCode($rule, $number)
    {
        $ruleLength = count($rule);
        $verifyConds = (array) str_split($number);

        $sum = 0;

        foreach ($verifyConds as $index => $verify) {
            $sum += (int) $verify * $rule[(int) $index % $ruleLength];
        }

        return static::getLastNumber($sum);
    }

    /**
     * @param int $number
     *
     * @return int
     */
    protected function getLastNumber($number)
    {
        return $number % 10;
    }
}
