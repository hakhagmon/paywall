<?php

namespace App\Payment\Handler;

use App\Payment\Dto\PaymentDataDTO;
use App\Payment\Exceptions\ValidateException;
use App\Schemas\Payment\AlfaJsonSchema;
use App\Schemas\Payment\SberJsonSchema;
use App\Service\JsonSchemaValidator;

class SberbankHandler implements PaymentHandlerInterface
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
            userId: $data['user_id'],
            languageCode: $data['language_code'],
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
        (new JsonSchemaValidator)->validate($data, SberJsonSchema::POST_REQUEST);
    }
}
