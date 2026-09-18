<?php

declare(strict_types=1);

namespace Presentation\Console\Commands;

use Illuminate\Console\Command;
use Infrastructure\MQ\Contracts\MessagePublisherInterface;

class MQPublishCommand extends Command
{
    protected $signature = 'mq:publish {queue=order_created} {message=Hello RabbitMQ} {--fail : Giả lập message gây lỗi để test DLQ}';

    protected $description = 'Publish a test message to RabbitMQ queue';

    public function handle(MessagePublisherInterface $publisher): int
    {
        $queue = $this->argument('queue');
        $text = $this->argument('message');
        $shouldFail = $this->option('fail');

        $payload = [
            'event'       => 'OrderCreated',
            'data'        => $text,
            'should_fail' => (bool) $shouldFail,
            'created_at'  => now()->toIso8601String(),
        ];

        // RabbitMQ direct publish: dùng default exchange ('') với routingKey là tên queue
        $publisher->publish(
            topicOrExchange: '',
            routingKey: $queue,
            payload: $payload
        );

        $this->info("Published successfully to [{$queue}]: " . json_encode($payload));

        return self::SUCCESS;
    }
}
