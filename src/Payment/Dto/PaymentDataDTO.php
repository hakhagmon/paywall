<?php

namespace App\Payment\Dto;

readonly class PaymentDataDTO
{
    public function __construct(
        private string  $token,
        private string  $status,
        private int     $orderId,
        private int     $amount,
        private string  $currency,
        private ?string $errorCode = null,
        private ?string $pan = null,
        private ?string $userId = null,
        private ?string $languageCode = null,
    )
    {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getPan(): ?string
    {
        return $this->pan;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }
}
