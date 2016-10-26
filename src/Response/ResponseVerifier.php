<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Response;

use Carbon\Carbon;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Response\ResponseFacilityTrait as ResponseFacility;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Response\ResponseParseTrait as ResponseParse;

class ResponseVerifier implements ParseInterface
{
    use ResponseParse, ResponseFacility;

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
     * @return string
     */
    private function getPlatformCode()
    {
        return $this->getParsed()->get('CODE');
    }

    /**
     * @return string
     */
    public function getPlatform()
    {
        $code = $this->getPlatformCode();

        return (array_key_exists($code, $this->facilities)) ?
            $this->facilities[$code] : $code;
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
        $date = sprintf('%014s', $this->getParsed()->get('TXN-DATE').$this->getParsed()->get('TIME'));

        return (new Carbon($date))
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
