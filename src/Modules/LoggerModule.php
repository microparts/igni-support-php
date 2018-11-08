<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 01/11/2018
 */

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Providers\ServiceProvider;
use Microparts\Configuration\Configuration;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggerModule implements ServiceProvider
{
    /**
     * @param \Igni\Container\ServiceLocator|ContainerInterface $container
     */
    public function provideServices($container): void
    {
        $container->share(LoggerInterface::class, function (ContainerInterface $container) {
            $logger = new Logger('App');
            $logger->pushHandler(new ErrorLogHandler(
                ErrorLogHandler::OPERATING_SYSTEM,
                $this->chooseLogLevel($container)
            ));

            return $logger;
        });
    }

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @return int
     */
    protected function chooseLogLevel(ContainerInterface $container): int
    {
        $conf = $container->get(Configuration::class);

        $level = Logger::INFO;
        if ($conf->get('debug', false)) {
            $level = Logger::DEBUG;
        }

        return $level;
    }
}
