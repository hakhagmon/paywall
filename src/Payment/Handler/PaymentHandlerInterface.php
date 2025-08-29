<?php

namespace App\Payment\Handler;

use App\Payment\Dto\PaymentDataDTO;

interface PaymentHandlerInterface
{

    public function __construct(string $data);

    public function getPaymentDTO(): PaymentDataDTO;
}
