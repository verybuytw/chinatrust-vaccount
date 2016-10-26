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

### Use ResponseVerifier verify response


```php
<?php
    use VeryBuy\Payment\ChinaTrust\VirtualAccount\Response\ResponseVerifier;

    $verifier = new ResponseVerifier({response encrypted string});

    $verifier->getTradedAt();       // 交易時間
    $verifier->getPaidAt();         // 付款時間
    $verifier->getVirtualAccount(); // 取得被付款虛擬帳號
    $verifier->getAmount();         // 付款金額
```

--

 - [x] VerifyType::NONE_BASE              (不檢)
 - [x] VerifyType::SINGLE_AMOUNT          (單碼檢核含金額)
 - [x] VerifyType::SINGLE_AMOUNT_DATE     (單碼檢核含金額及日期)
