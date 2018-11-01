<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 01/11/2018
 */

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Providers\ServiceProvider;
use Psr\Container\ContainerInterface;
use Tmconsulting\Configuration;

class ConfigurationModule implements ServiceProvider
{
    /**
     * @param \Igni\Container\ServiceLocator|ContainerInterface $container
     */
    public function provideServices($container): void
    {
        $container->share(Configuration::class, function () {
            return new Configuration();
        });
    }
}
