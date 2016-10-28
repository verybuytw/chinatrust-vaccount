<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount\Response;

trait ResponsePlatformTrait
{
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
     * @return boolean
     */
    public function isIBON()
    {
        return ($this->getPlatformCode() == ResponseCode::PLATFORM_IBON);
    }

    /**
     * @return boolean
     */
    public function isLIFE()
    {
        return ($this->getPlatformCode() == ResponseCode::PLATFORM_LIFE);
    }

    /**
     * @return boolean
     */
    public function isFAMI()
    {
        return ($this->getPlatformCode() == ResponseCode::PLATFORM_FAMI);
    }

    /**
     *
     * @return boolean
     */
    public function isStore()
    {
        return in_array($this->getPlatformCode(), [
            ResponseCode::PLATFORM_IBON,
            ResponseCode::PLATFORM_LIFE,
            ResponseCode::PLATFORM_FAMI,
        ]);
    }
}
