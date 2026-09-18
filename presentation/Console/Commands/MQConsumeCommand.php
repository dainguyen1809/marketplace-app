<?php

declare(strict_types=1);

namespace Presentation\Console\Commands;

use Illuminate\Console\Command;
use Infrastructure\MQ\Contracts\MessageConsumerInterface;
use RuntimeException;

class MQConsumeCommand extends Command
{
    protected $signature = 'mq:consume {queue=order_created} {--prefetch=1}';

    protected $description = 'Start consuming messages from a RabbitMQ queue';

    public function handle(MessageConsumerInterface $consumer): int
    {
        $queue = $this->argument('queue');
        $prefetch = (int) $this->option('prefetch');

        $this->info("Worker is listening on queue [{$queue}] (prefetch: {$prefetch})... Nhấn Ctrl+C để dừng.");

        $consumer->consume(
            queueOrTopic: $queue,
            handler: function (array $payload, $message): void {
                $this->line('Nhận được message: '.json_encode($payload));

                // Giả lập lỗi nếu có cờ should_fail để kiểm tra cơ chế tự bay sang DLQ
                if (! empty($payload['should_fail'])) {
                    $this->error('Lỗi giả lập phát sinh! Message sẽ bị NACK và tự động đẩy sang DLQ.');
                    throw new RuntimeException('Simulated processing error for testing DLQ');
                }

                $this->info('Xử lý thành công! Gửi ACK.');
            },
            prefetchCount: $prefetch
        );

        return self::SUCCESS;
    }
}
