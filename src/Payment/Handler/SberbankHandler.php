<?php

namespace App\Payment\Handler;

use App\Payment\Dto\PaymentDataDTO;
use App\Schemas\Payment\SberJsonSchema;
use App\Service\JsonSchemaValidator;

class SberbankHandler implements PaymentHandlerInterface
{
    private PaymentDataDTO $PaymentDTO;

    public function __construct(string $data)
    {
        $data = json_decode($data, true);
        $this->validate($data);
        $this->PaymentDTO = new PaymentDataDTO(
            token: $data['token'],
            status: $data['status'],
            orderId: $data['order_id'],
            amount: $data['amount'],
            currency: $data['currency'],
            errorCode: $data['error_code'] ?? null,
            pan: $data['pan'],
            userId: $data['user_id'],
            languageCode: $data['language_code'],
        );
    }


    public function getPaymentDTO(): PaymentDataDTO
    {
        return $this->PaymentDTO;
    }

    private function validate($data)
    {
       return (new JsonSchemaValidator)->validate($data, SberJsonSchema::POST_REQUEST);
    }
}
