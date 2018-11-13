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
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ConfigurationModule implements ServiceProvider
{
    /**
     * @param \Illuminate\Container\Container|ContainerInterface $container
     */
    public function provideServices($container): void
    {
        $container->bind(Configuration::class, function (ContainerInterface $container) {
            $conf = new Configuration('./configuration');
            $conf->setLogger($container->get(LoggerInterface::class));

            return $conf->load();
        });
    }
}
