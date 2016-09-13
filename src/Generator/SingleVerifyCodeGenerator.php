<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Generator;

use Carbon\Carbon;

class SingleVerifyCodeGenerator extends VerifyCodeGeneratorContract implements VerifyInterface
{
    use VerifyCodeRule;

    const VERIFY_CODE_BASE = [3, 7, 1];
    const VERIFY_CODE_AMOUNT = [3, 7, 1];

    /**
     * @param string    $account
     * @param string    $number
     * @param type      $amount
     *
     * @return string virtual account
     */
    public function buildWithAmount($account, $number, $amount)
    {
        $account = $account.$number;

        $v1 = $this->genVerifyCode(static::VERIFY_CODE_BASE, $account);
        $v2 = $this->genVerifyCode(static::VERIFY_CODE_AMOUNT, $amount);

        return $account.((10 - ($v1 + $v2)) % 10);
    }

    /**
     * @param string    $account
     * @param string    $number
     * @param type      $amount
     * @param timestamp $date
     *
     * @return string virtual account
     */
    public function buildWithAmountAndDate($account, $number, $amount, $date)
    {
        $dt = Carbon::createFromTimestamp($date)->addDay();

        $number = substr($dt->year, -1, 1).sprintf('%03s', $dt->dayOfYear).$number;

        return $this->buildWithAmount($account, $number, $amount);
    }
}
