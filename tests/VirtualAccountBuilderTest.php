<?php

namespace Tests\Payment\ChinaTrust\VirtualAccount;

use VeryBuy\Payment\ChinaTrust\VirtualAccount\VerifyType;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\VirtualAccountBuilder;

class VirtualAccountBuilderTest extends AbstractTestCase
{
    const COMPANY_ID = 98102;

    public function testBuilderWithoutVerifyCode()
    {
        $builder = new VirtualAccountBuilder(static::COMPANY_ID, [
            'type' => VerifyType::NONE_BASE,
            'number' => 123456789,  // 自訂碼 length:9
        ]);

        $this->assertEquals(98102123456789, $builder->make());
    }

    public function testBuilderWithSingleGenerator()
    {
        $builder = new VirtualAccountBuilder(static::COMPANY_ID, [
            'type' => VerifyType::SINGLE_AMOUNT,
            'amount' => 50943,      // 金額     max length:10
            'number' => 90010303,   // 自訂碼   length:8
        ]);

        $this->assertEquals(98102900103031, $builder->make());

        $builder = new VirtualAccountBuilder(static::COMPANY_ID, [
            'type' => VerifyType::SINGLE_AMOUNT_DATE,
            'amount' => 50943,      // 金額     max length:10
            'number' => '0303',     // 自訂碼   length:4
            'expired_at' => strtotime('1999-01-01')
        ]);

        $this->assertEquals(98102900103031, $builder->make());
    }
}
