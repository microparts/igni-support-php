<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 01/11/2018
 */

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Providers\ServiceProvider;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggerModule implements ServiceProvider
{
    /**
     * @param \Illuminate\Container\Container|ContainerInterface $container
     */
    public function provideServices($container): void
    {
        $container->bind(LoggerInterface::class, function () {
            $logger = new Logger('App');
            $logger->pushHandler(new ErrorLogHandler(
                ErrorLogHandler::OPERATING_SYSTEM,
                $this->chooseLogLevel()
            ));

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
