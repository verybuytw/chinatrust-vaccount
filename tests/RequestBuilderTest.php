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
            'id' => 81842,
            'name' => 'VERYBUY',
        ]);

        $this->assertGreaterThanOrEqual(20, $builder->genTransactionId());
    }
}
