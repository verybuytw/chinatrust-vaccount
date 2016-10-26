<?php

namespace Tests\Payment\ChinaTrust\VirtualAccount;

use VeryBuy\Payment\ChinaTrust\VirtualAccount\Response\ResponseVerifier;

class ResponseVerifierTest extends AbstractTestCase
{
    protected $bodyStub;

    public function setUp()
    {
        $this->bodyStub = '62454015534121051026618305270     00000000143210010510261112177007959582C+98982080301700000031496**0884*           81842000000000000000000000959582                                                                                                                                                                   ';
    }

    public function testResponseVerifierParseBody()
    {
        $verifier = new ResponseVerifier($this->bodyStub);

        $parsed = $verifier->getParsed();

        $this->assertArrayHasKey('CODE', $parsed);
        $this->assertArrayHasKey('DATE', $parsed);
        $this->assertArrayHasKey('TXN-DATE', $parsed);
        $this->assertArrayHasKey('TIME', $parsed);
        $this->assertArrayHasKey('MEMO-NO', $parsed);
        $this->assertArrayHasKey('COMPANY-NO', $parsed);
        $this->assertArrayHasKey('TXN-AMT', $parsed);
    }
}
