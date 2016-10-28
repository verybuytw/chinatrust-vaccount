<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Response;

interface ResponseCode
{
    const TYPE_SUCCESS = '0000';
    const PLATFORM_IBON = 'V';
    const PLATFORM_LIFE = 'L';
    const PLATFORM_FAMI = 'K';
}
