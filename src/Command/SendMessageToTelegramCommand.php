<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\TelegramMessageQueue;
use App\Repository\TelegramMessageQueueRepository;
use App\Service\TelegramService;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

#[AsCommand(
    name: 'app:send-message-to-telegram',
    description: 'отправка сообщений в Telegram'
)]
class SendMessageToTelegramCommand extends Command
{
    const int STATUS_DONE = 1;
    const int STATUS_BLOCKED = 2;
    private SymfonyStyle $io;

    public function __construct(
        private TelegramMessageQueueRepository $repository,
        private EntityManagerInterface         $entityManager
    )
    {
        parent::__construct();
    }

    /**
     * @param int $status
     * @param TelegramMessageQueue $message
     * @return void
     */
    public function updateStatus(int $status, TelegramMessageQueue $message): void
    {
        $message->setStatus($status);
        $message->setSendedAt($status === 1 ? new DateTimeImmutable() : null);
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_OPTIONAL,
                'кол-во сообщений за 1 раз',
                100
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //todo тут нужен "симофор" для проверки, что сейчас процесс не запущен, чтоб избежать дублей отправки
        $this->io = new SymfonyStyle($input, $output);
        $limit = (int)$input->getOption('limit');

        // Получаем сообщения со статусом 0 (не отправленные)
        $messages = $this->repository->findPendingMessagesLimit($limit);

        if (empty($messages)) {
            $this->io->success('Очередь пуста');
            return Command::SUCCESS;
        }

        $this->io->text('Сообщений в очереди:' . count($messages));

        foreach ($messages as $message) {
            try {

                $status = $this->sendToTelegram($message->getUserId(), $message->getMessage());

                $this->updateStatus($status, $message);

            } catch (\Exception $e) {
                $this->io->error('Ошибка ' . $message->getId() . ' ' . $e->getMessage());
            }
        }


        $this->io->newLine();
        $this->io->success('Все отправлено');

        return Command::SUCCESS;
    }


    /**
     * @param string $userId
     * @param string $message
     * @return int
     */
    private function sendToTelegram(string $userId, string $message): int
    {
        sleep(1);
        $result = TelegramService::sendMessage($userId, $message);

        if (empty($result['error'])) {
            return self::STATUS_DONE;
        }

        if (in_array($result['error'], [Response::HTTP_TOO_MANY_REQUESTS, Response::HTTP_INTERNAL_SERVER_ERROR])) {
            $this->io->warning('Ошибка, останавливаем процесс');
            die();
        }

        return self::STATUS_BLOCKED;
    }
}
