<?php declare(strict_types=1);

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Providers\ServiceProvider;
use Microparts\Logger\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggerModule implements ServiceProvider
{
    /**
     * @param \Illuminate\Container\Container|ContainerInterface $container
     */
    public function provideServices($container): void
    {
        $container->singleton(LoggerInterface::class, function () {
            return Logger::new('App', getenv('DEBUG') === 'true');
        });
    }
}
