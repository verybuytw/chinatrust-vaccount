<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount;

/**
 * Note: can not use zero to be value.
 */
interface VerifyType
{
    const NONE_BASE = 1;
    const SINGLE_AMOUNT = 11;
    const SINGLE_AMOUNT_DATE = 12;
}
