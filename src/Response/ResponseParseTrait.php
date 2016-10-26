<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Response;

use Illuminate\Support\Collection;

trait ResponseParseTrait
{
    /**
     * @var mixed array|null
     */
    protected $parse;

    /**
     * @return Collection
     */
    public function getParsed()
    {
        return $this->parse;
    }

    /**
     * @param string $parse
     *
     * @return Collection
     */
    public function parse($parse)
    {
        return Collection::make([
            'ACCT-NO' => mb_substr($parse, 0, 12),
            'ACCT-CODE' => mb_substr($parse, 12, 1),
            'DATE' => mb_substr($parse, 13, 7),
            'MEMO-NO' => mb_substr($parse, 20, 14),
            'TXN-AMT' => mb_substr($parse, 34, 15),
            'TXN-DATE' => mb_substr($parse, 49, 7),
            'TIME' => mb_substr($parse, 56, 6),
            'SEQ-NO' => mb_substr($parse, 62, 10),
            'DC' => mb_substr($parse, 72, 1),
            'SIGN' => mb_substr($parse, 73, 1),
            'TELLER-NO' => mb_substr($parse, 74, 5),
            'TXN-CODE' => mb_substr($parse, 79, 5),
            'CODE' => mb_substr($parse, 84, 1),
            'ACT-NO-OUT-1' => mb_substr($parse, 85, 3),
            'ACT-NO-OUT-2' => mb_substr($parse, 88, 16),
            'MEMO-11' => mb_substr($parse, 104, 11),
            'COMPANY-NO' => mb_substr($parse, 115, 5),
            'YEAR' => mb_substr($parse, 120, 3),
            'PAYER-ACNO' => mb_substr($parse, 123, 8),
            'STOCK-NO' => mb_substr($parse, 131, 9),
            'TYPE' => mb_substr($parse, 140, 1),
            'SK-SEQ-NUM' => mb_substr($parse, 141, 6),
            'FILLER' => mb_substr($parse, 147, 3),
            'RMTR-NAME' => mb_substr($parse, 150, 80),
            'NOTE' => mb_substr($parse, 230, 80),
        ]);
    }
}
