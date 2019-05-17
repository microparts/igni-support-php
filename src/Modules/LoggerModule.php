<?php declare(strict_types=1);

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Providers\ServiceProvider;
use Microparts\Logger\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LoggerModule implements ServiceProvider
{
    /**
     * @param \Illuminate\Container\Container|ContainerInterface $container
     */
    public function provideServices($container): void
    {
        $container->singleton(LoggerInterface::class, function () {
            $level = getenv('DEBUG') === 'true'
                ? LogLevel::DEBUG
                : LogLevel::INFO;

            return Logger::default(Logger::CHANNEL, $level);
        });
    }
}
