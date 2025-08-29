<?php

declare(strict_types=1);

namespace App\Controller;

use App\Payment\Const\PaymentStatus;
use App\Payment\Const\PaymentType;
use App\Payment\Dto\PaymentDataDTO;
use App\Payment\Handler\AlfabankHandler;
use App\Payment\Handler\PaymentHandlerInterface;
use App\Payment\Handler\SberbankHandler;
use App\Payment\Service\PaymentMessageSelector;
use App\Payment\Service\PaymentSaver;
use App\Payment\Service\TelegramMessageSaver;
use App\Repository\PaymentRepository;
use App\Service\PaymentLogger;
use PHPUnit\Framework\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentCallbackController extends AbstractController
{

    public function __construct(
        private readonly PaymentSaver           $paymentSaver,
        private readonly PaymentLogger          $paymentLogger,
        private readonly TelegramMessageSaver   $telegramMessageSaver,
        private readonly PaymentMessageSelector $paymentMessageSelector,
        private readonly PaymentRepository      $paymentRepository,
    )
    {
    }

    /**
     * @param Request $request
     * @param string $type
     * @return Response
     */
    public function handle(Request $request, string $type): Response
    {
        try {
            $this->paymentLogger->log($type, $request->getContent()); //логируем входящие запросы в бд

            $payment = $this->getPaymentHandler($type, $request->getContent());

            $this->save($payment->getPaymentDTO());

            return $this->json(['status' => 'ok'])->setStatusCode(Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
            ])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param string $type
     * @param string $data
     * @return PaymentHandlerInterface
     */
    public function getPaymentHandler(string $type, string $data): PaymentHandlerInterface
    {
        return match ($type) {
            PaymentType::PAYMENT_TYPE_SBER => new SberbankHandler($data),
            PaymentType::PAYMENT_TYPE_ALFA => new AlfabankHandler($data),
            default => throw new Exception('payment type ' . $type . ' not found')
        };
    }

    /**
     * @param PaymentDataDTO $PaymentDTO
     * @return void
     */
    private function savePayment(PaymentDataDTO $PaymentDTO): void
    {
        $this->paymentSaver->save($PaymentDTO);
    }

    /**
     * @param PaymentDataDTO $PaymentDTO
     * @return void
     */
    private function save(PaymentDataDTO $PaymentDTO): void
    {
        $this->savePayment($PaymentDTO);
        $this->saveMessage($PaymentDTO->getUserId(), $PaymentDTO->getStatus(), $PaymentDTO->getLanguageCode());
    }

    /**
     * @param string $UserId
     * @param string $Status
     * @param string $LanguageCode
     * @return void
     */
    private function saveMessage(string $UserId, string $Status, string $LanguageCode): void
    {
        if ($this->paymentRepository->countByUserIdAndStatusSimple($UserId, PaymentStatus::CONFIRMED) > 0) {
            //будем учитывать только тех кто ранее оплачивал
            $Status = PaymentStatus::EXTENDED;
        }

        $text_message = $this->paymentMessageSelector->getPaymentMessage($Status, $LanguageCode);
        $this->telegramMessageSaver->save($UserId, $text_message);
    }
}
