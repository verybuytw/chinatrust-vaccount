<?php

namespace Tests\Payment\ChinaTrust\VirtualAccount;

use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\RequestBuilder;

class RequestBuilderTest extends AbstractTestCase
{
    protected $wsdl;

    public function setUp()
    {
        $base_path = dirname(__DIR__);
        $wsdl_path = $base_path.DIRECTORY_SEPARATOR.'resources';
        $this->wsdl = $wsdl_path.DIRECTORY_SEPARATOR.'InstnCollPmt.sit.wsdl';
    }

    public function testWsdlExists()
    {
        $this->assertFileExists($this->wsdl);
    }

    public function testGetSoapClientWithHeader()
    {
        $builder = new RequestBuilder([
            'wsdl' => $this->wsdl,
            'company' => [
                'id' => 81842,
                'number' => '03077208',
                'name' => 'VERYBUY',
                'alias' => '非常科技',
            ]
        ]);
        
        $body = '62454015534121051026618305270     00000000143210010510261112177007959582C+98982080301700000031496**0884*           81842000000000000000000000959582                                                                                                                                                                   ';
        $body = '10711845580620980928001017799     00000000011980009809260831308222732564C+964100803010540000000000000000           81498000000000000000000000000000                                                                                                                                                                   ';
        dump($body);
        dump(mb_substr($body, 0, 12));
        dump(mb_substr($body, 12, 1));
        dump(mb_substr($body, 13, 7));
        dump(mb_substr($body, 20, 14));
        dump(mb_substr($body, 34, 15));
        dump(mb_substr($body, 49, 7));
        dump(mb_substr($body, 56, 6));
        dump(mb_substr($body, 62, 10));
        dump(mb_substr($body, 72, 1));
        dump(mb_substr($body, 73, 1));
        dump(mb_substr($body, 74, 5));
        dump(mb_substr($body, 79, 5));
        dump(mb_substr($body, 84, 1));
        dump(mb_substr($body, 85, 3));
        dump(mb_substr($body, 88, 16));
        dump(mb_substr($body, 104, 11));
        dump(mb_substr($body, 115, 5));
        dump(mb_substr($body, 120, 3));
        dump(mb_substr($body, 123, 8));
        dump(mb_substr($body, 131, 9));
        dump(mb_substr($body, 140, 1));
        dump(mb_substr($body, 141, 6));
        dump(mb_substr($body, 147, 3));
        dump(mb_substr($body, 150, 80));
        dump(mb_substr($body, 230, 80));

//        $this->assertGreaterThanOrEqual(20, $builder->genTransactionId());
    }
}
