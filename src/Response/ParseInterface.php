<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Response;

interface ParseInterface
{
    /**
     * @param array $parse
     */
    public function parse($parse);
}
