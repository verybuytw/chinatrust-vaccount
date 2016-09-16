<?php

namespace Tests\Payment\ChinaTrust\VirtualAccount;

use stdClass;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\RequestBuilder;

class RequestBuilderTest extends AbstractTestCase
{
    protected $wsdl;

    public function setUp()
    {
        $base_path = dirname(__DIR__);
        $wsdl_path = $base_path.DIRECTORY_SEPARATOR.'wsdl';
        $this->wsdl = $wsdl_path.DIRECTORY_SEPARATOR.'InstnCollPmt.sit.wsdl';
    }

    public function testWsdlExists()
    {
        $this->assertFileExists($this->wsdl);
    }

    public function testGetSoapClientWithHeader()
    {
        $builder = new RequestBuilder(81842, $this->wsdl);

        $this->assertGreaterThanOrEqual(20, $builder->genTransactionId());
    }

    public function testGetSoapClientRequest()
    {
        $builder = new RequestBuilder(81842, $this->wsdl);

        $builder->make(static::paymentStub());

        dd($builder);
    }

    private function paymentStub()
    {
        $builder = new RequestBuilder(81842, $this->wsdl);

        $vaccount = 81842234567891;

        $ref = new stdClass();
        $ref->RefType = 'StoreBarcode1';
        $ref->RefId = '自訂欄位值ABC';

        $ref1 = new stdClass();
        $ref1->RefType = '自訂欄位名稱DEF';
        $ref1->RefId = '自訂欄位值DEF';

        $ref2 = new stdClass();
        $ref2->RefType = '自訂欄位名稱GHI';
        $ref2->RefId = '自訂欄位值GHI';

        $ref3 = new stdClass();
        $ref3->RefType = '自訂欄位名稱JKL';
        $ref3->RefId = '自訂欄位值JKL';

        $ref4 = new stdClass();
        $ref4->RefType = '自訂欄位名稱MNO';
        $ref4->RefId = '自訂欄位值MNO';

        $ref5 = new stdClass();
        $ref5->RefType = '自訂欄位名稱PQR';
        $ref5->RefId = '自訂欄位值PQR';

//        $storeAgent1 = new stdClass();
//        $storeAgent1->RefType = 'StoreBarcode1';
//        $storeAgent1->RefId = $vaccount;
        $storeAgent2 = new stdClass();
        $storeAgent2->RefType = 'StoreBarcode2';
        $storeAgent2->RefId = $vaccount;

        $amount = new stdClass();
        $amount->Amt = 100;
        $amount1 = new stdClass();
        $amount1->Amt = 100;

        $fee = new stdClass();
        $fee->CurAmt = $amount;

        $bankAmount = new stdClass();
        $bankAmount->Amt = 100;
        $bankFee = new stdClass();
        $bankFee->CurAmt = $bankAmount;
//        $bankRef1 = new stdClass;
//        $bankRef1->RefType = 'BankBarcode1';
//        $bankRef1->RefId = 'Barcode';
        $bankRef2 = new stdClass();
        $bankRef2->RefType = 'BankBarcode2';
        $bankRef2->RefId = $vaccount;
        $bankSettlement = new stdClass();
        $bankSettlement->SettlementMethod = 'Bank';
        $bankSettlement->Fee = $bankFee;
        $bankSettlement->RefInfo = $bankRef2;
//        $bankSettlement->Memo = '文件上不是說這個欄位是 option 嗎';

//        $postAmount = new stdClass();
//        $postAmount->Amt = 100;
//        $postFee = new stdClass();
//        $postFee->CurAmt = $postAmount;
//        $postRef1 = new stdClass();
//        $postRef1->RefType = 'PostBarcode1';
//        $postRef1->RefId = 'Barcode';
//        $postRef2 = new stdClass();
//        $postRef2->RefType = 'PostBarcode2';
//        $postRef2->RefId = 'Barcode';
//        $postSettlement = new stdClass();
//        $postSettlement->SettlementMethod = 'PostOffice';
//        $postSettlement->Fee = $postFee;
//        $postSettlement->RefInfo = [$postRef1, $postRef2];
//        $postSettlement->Memo = '文件上不是說這個欄位是 option 嗎';
        $Memo1 = sprintf('%- 50s', 'T000000000000003');
        $Memo2 = sprintf('%- 50s', '訂單編號');
        $storeSettlement = new stdClass();
        $storeSettlement->SettlementMethod = 'StoreAgent';
        $storeSettlement->Fee = $fee;
        $storeSettlement->RefInfo = $storeAgent2;
        $storeSettlement->Memo = $Memo1.$Memo2;
        $billsumamout = new stdClass();
        $billsumamout->ShortDesc = '費用明細1';
        $billsumamout->Desc = '費用明細說明1';
        $billsumamout->CurAmt = $amount1;
        $bc_addr = new stdClass();
        $bc_addr->Addr = '繳款人地址';
        $bc_addr->PostalCode = 10523;
        $bill_contact = new stdClass();
        $bill_contact->PostAddr = $bc_addr;
        $bill_contact->EmailAddr = 'hughes@abc.com';
        $bill = new stdClass();
        $bill->CustPermId = 'A123456789';
        $bill->BillingAcct = '繳款人識別碼';
        $bill->Name = '繳款人姓名';
        $bill->ContactInfo = $bill_contact;
        $bill->BillDt = '2016-10-01';
        $bill->DueDt = '2016-08-04';
        $bill->BillRefInfo = '注意事項';
        $bill->Memo = '備註';
        $bill->RefInfo = [$ref1, $ref5];
        $bill->SettlementInfo = [$bankSettlement, $storeSettlement];
        $bill->BillSummAmt = $billsumamout;

        $addr = new stdClass();
        $addr->Addr = '公司住址';
        $contact = new stdClass();
        $contact->PostAddr = $addr;
        $contact->Phone = '02-2222-7777';
        $biller = new stdClass();
        $biller->IndustNum = '03077208';
        $biller->BussType = '07914';
        $biller->Name = '公司名稱';
        $biller->ContactInfo = $contact;

        $info = new stdClass();
        $info->BillerInfo = $biller;
        $info->BillInfo = $bill;
        $info->RefInfo = [$ref2, $ref4];

        $params = new stdClass();
        $params->TxnCode = 'InstnCollPmtInstAdd';
        $params->RqUID = $builder->genTransactionId();
        $params->CollPmtInstInfo = $info;

        return $params;
    }
}
