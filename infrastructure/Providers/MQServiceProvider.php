<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Infrastructure\MQ\Contracts\MessageConsumerInterface;
use Infrastructure\MQ\Contracts\MessagePublisherInterface;
use Infrastructure\MQ\RabbitMQ\RabbitMQClient;
use Presentation\Console\Commands\MQConsumeCommand;
use Presentation\Console\Commands\MQPublishCommand;

final class MQServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RabbitMQClient::class);

        $this->app->bind(MessagePublisherInterface::class, RabbitMQClient::class);
        $this->app->bind(MessageConsumerInterface::class, RabbitMQClient::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MQPublishCommand::class,
                MQConsumeCommand::class,
            ]);
        }
    }
}
