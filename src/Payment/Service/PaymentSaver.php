<?php

namespace App\Payment\Service;

use App\Entity\Payment;
use App\Payment\Dto\PaymentDataDTO;
use Doctrine\ORM\EntityManagerInterface;

readonly class PaymentSaver
{
    public function save(PaymentDataDTO $dto): Payment
    {
        $payment = new Payment();
        $payment->setToken($dto->getToken());
        $payment->setStatus($dto->getStatus());
        $payment->setOrderId($dto->getOrderId());
        $payment->setAmount($dto->getAmount());
        $payment->setCurrency($dto->getCurrency());
        $payment->setErrorCode($dto->getErrorCode());
        $payment->setPan($dto->getPan());
        $payment->setUserId($dto->getUserId());
        $payment->setLanguageCode($dto->getLanguageCode());

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $payment;
    }

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }
}
