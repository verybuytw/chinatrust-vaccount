<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Generator;

interface VerifyInterface
{
    /**
     * @param string    $account    Company id from ChinaTrust.
     * @param string    $number
     * @param int       $amount
     *
     * @return string virtual account with amount
     */
    public function buildWithAmount($account, $number, $amount);

    /**
     * @param string    $account    Company id from ChinaTrust.
     * @param string    $number
     * @param int       $amount
     * @param timestamp $date       Expire date
     *
     * @return string virtual account with amount
     */
    public function buildWithAmountAndDate($account, $number, $amount, $date);
}
