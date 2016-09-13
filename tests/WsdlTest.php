<?php

namespace Tests\Payment\ChinaTrust\VirtualAccount;

class WsdlTest extends AbstractTestCase
{
    protected $wsdl;

    public function setUp()
    {
        $base_path = dirname(__DIR__);
        $wsdl_path = $base_path.DIRECTORY_SEPARATOR.'wsdl';
        $this->wsdl = $wsdl_path.DIRECTORY_SEPARATOR.'InstnCollPmt.wsdl';
    }

    public function testWsdlExists()
    {
        $this->assertFileExists($this->wsdl);
    }
}
