<?php

namespace App\Payment\Handler;

use App\Payment\Dto\PaymentDataDTO;
use App\Payment\Exceptions\ValidateException;
use App\Schemas\Payment\AlfaJsonSchema;
use App\Schemas\Payment\SberJsonSchema;
use App\Service\JsonSchemaValidator;

class AlfabankHandler implements PaymentHandlerInterface
{
    private PaymentDataDTO $PaymentDTO;

    public function __construct(string $data)
    {
        $this->validate($data);
        $data = json_decode($data, true);
        $this->PaymentDTO = new PaymentDataDTO(
            token: $data['token'],
            status: $data['status'],
            orderId: $data['order_id'],
            amount: $data['amount'],
            currency: $data['currency'],
            errorCode: $data['error_code'] ?? null,
            pan: $data['pan'],
            userId: $data['user'],
            languageCode: $data['language'],
        );
    }


    public function getPaymentDTO(): PaymentDataDTO
    {
        return $this->PaymentDTO;
    }

    /**
     * @throws ValidateException
     */
    private function validate(string $data): void
    {
       (new JsonSchemaValidator)->validate($data, AlfaJsonSchema::POST_REQUEST);
    }
}
