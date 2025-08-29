<?php

namespace App\Payment\Service;

use Symfony\Contracts\Translation\TranslatorInterface;

readonly class PaymentMessageSelector
{

    public function __construct(
        private readonly TranslatorInterface $translator
    )
    {
    }

    public function getPaymentMessage(string $status, string $locale): string
    {
        return $this->translator->trans("payment.$status", [], 'payment_status', $locale);
    }

}
