<?php

namespace App\Payment\Service;

use App\Entity\Payment;
use App\Entity\TelegramMessageQueue;
use App\Payment\Dto\PaymentDataDTO;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

readonly class TelegramMessageSaver
{
    public final const int STATUS_MESSAGE_NEW = 0;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function save(string $user_id, $message): TelegramMessageQueue
    {
        $MessageQueue = new TelegramMessageQueue();
        $MessageQueue->setUserId($user_id);
        $MessageQueue->setStatus(self::STATUS_MESSAGE_NEW);
        $MessageQueue->setMessage($message);

        $this->entityManager->persist($MessageQueue);
        $this->entityManager->flush();

        return $MessageQueue;
    }
}
