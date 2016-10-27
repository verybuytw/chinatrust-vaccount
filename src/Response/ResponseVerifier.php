<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Response;

use Carbon\Carbon;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Response\ResponsePlatformTrait as ResponsePlatform;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Response\ResponseFacilityTrait as ResponseFacility;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Response\ResponseParseTrait as ResponseParse;

class ResponseVerifier implements ParseInterface
{
    use ResponseParse, ResponseFacility, ResponsePlatform;

    /**
     * @var string
     */
    protected $result;

    /**
     * @param string $result
     */
    public function __construct($result = null)
    {
        if (! is_null($result)) {
            $this->result = $result;
            $this->parse = static::parse($result);
        }
    }

    /**
     * @return Carbon
     */
    public function getTradedAt()
    {
        $date = sprintf('%08s', $this->getParsed()->get('DATE'));

        return (new Carbon($date))
            ->addYears(1911);
    }

    /**
     * @return Carbon
     */
    public function getPaidAt()
    {
        $dateTime = sprintf('%014s', $this->getParsed()->get('TXN-DATE').$this->getParsed()->get('TIME'));

        return (new Carbon($dateTime))
            ->addYears(1911);
    }

    /**
     * @return string
     */
    public function getVirtualAccount()
    {
        return strtr('{companyId}{serialNumber}', [
            '{companyId}' => $this->getParsed()->get('COMPANY-NO'),
            '{serialNumber}' => trim($this->getParsed()->get('MEMO-NO')),
        ]);
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return intval($this->getParsed()->get('TXN-AMT') / 100);
    }
}
