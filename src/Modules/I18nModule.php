<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 01/11/2018
 */

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Providers\ServiceProvider;
use Igni\Network\Http\ServerRequest;
use Microparts\Configuration\Configuration;
use Microparts\I18n\I18nInterface;
use Microparts\I18n\Manager;
use Psr\Container\ContainerInterface;

class I18nModule implements ServiceProvider
{
    /**
     * @param \Igni\Container\ServiceLocator|ContainerInterface $container
     */
    public function provideServices($container): void
    {
        $container->share(I18nInterface::class, function (ContainerInterface $container) {
            $manager = new Manager($container->get(Configuration::class));
            return $manager->withMessage(ServerRequest::fromGlobals())->load();
        });
    }
}
