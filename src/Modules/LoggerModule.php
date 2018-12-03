<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 01/11/2018
 */

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Providers\ServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggerModule implements ServiceProvider
{
    private const CHANNEL = 'App';

    /**
     * @param \Illuminate\Container\Container|ContainerInterface $container
     */
    public function provideServices($container): void
    {
        // Create Monolog logger without fucking brackets -> [] []  [] []  [] []  [] []  [] []
        // if context and extra is empty.
        $container->bind(LoggerInterface::class, function () {
            $logger = new Logger(self::CHANNEL);
            $handler = new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $this->chooseLogLevel());
            $formatter = new LineFormatter('[%datetime%] %channel%.%level_name%: %message% %context% %extra%');
            $formatter->ignoreEmptyContextAndExtra();

            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);

            return $logger;
        });
    }

    /**
     * @return int
     */
    protected function chooseLogLevel(): int
    {
        $level = Logger::INFO;
        if (getenv('DEBUG') === 'true') {
            $level = Logger::DEBUG;
        }

        return $level;
    }
}
