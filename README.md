Installation
-------------

```shell
$    composer require vb-payment/chinatrust-vaccount
```

### Use VirtualAccountBuilder generate form for chinatrust virtual account


```php
<?php

    use VeryBuy\Payment\ChinaTrust\VirtualAccount\VerifyType;
    use VeryBuy\Payment\ChinaTrust\VirtualAccount\VirtualAccountBuilder;

    $companyId = 99123; // 特店代號

    $builder = new VirtualAccountBuilder($companyId, [
        'type' => VerifyType::NONE_BASE,    // builder 類別
        'number' => '123456789',            // (14碼)自訂碼 length:9
    ]);

    $vaccount = $builder->make();
```


### Use RequestBuilder register virtual account number to ChinaTrust

 > before request, hosts have to append settings `175.184.247.21  hermes.ctbcbank.com`

```php
<?php
    use VeryBuy\Payment\ChinaTrust\VirtualAccount\Request\RequestBuilder;

    $response = (new RequestBuilder([
        'wsdl' => $wsdl,
        'company' => [
            'id' => 81842,
            'number' => 53538135,
            'name' => 'VERYBUY',
            'alias' => '非常科技',
        ],
    ]))->make([
        'channels' => [
            RequestBuilder::CHANNEL_BANK,
            RequestBuilder::CHANNEL_STORE,
        ],
        'customer' => [
            'mid' => '繳款人識別碼(MID1)',  // length:20
            'name' => '繳款人姓名',         // length:100
        ],
        'vaccount' => $vaccount,
        'amount' => 2000,
        'expired_at' => strtotime('2016-10-31'),
        'store' => [
            'field_name' => '訂單編號', // ibon 上顯示文字
            'field_value' => sprintf('T%015d', time()), // ibon 上顯示的值
        ],
    ]);
```

### Use ResponseVerifier verify response


```php
<?php
    use VeryBuy\Payment\ChinaTrust\VirtualAccount\Response\ResponseVerifier;

    $verifier = new ResponseVerifier({response encrypted string});

    $verifier->getTradedAt();       // 交易時間
    $verifier->getPaidAt();         // 付款時間
    $verifier->getVirtualAccount(); // 取得被付款虛擬帳號
    $verifier->getAmount();         // 付款金額
    

    // 當收到 Response 後的 HTTP CODE
    // 200 成功
    // 250 重複送(TransactionNo可為重複送的的判斷依據)
    // 543 失敗
```

--

 - [x] VerifyType::NONE_BASE              (不檢)
 - [x] VerifyType::SINGLE_AMOUNT          (單碼檢核含金額)
 - [x] VerifyType::SINGLE_AMOUNT_DATE     (單碼檢核含金額及日期)
