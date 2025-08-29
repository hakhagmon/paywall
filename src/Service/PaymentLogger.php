<?php


namespace App\Service;

use App\Entity\PaymentRequestLog;
use Doctrine\ORM\EntityManagerInterface;

readonly class PaymentLogger
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * Логирует входящий платежный запрос
     */
    public function log(string $paymentType, ?string $body = null): void
    {
        $log = new PaymentRequestLog();
        $log->setPaymentType($paymentType);
        $log->setBody($body);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
