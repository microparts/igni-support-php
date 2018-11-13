<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 01/11/2018
 */

namespace Microparts\Igni\Support\Providers;

use Igni\Application\Providers\ControllerProvider;

class ControllerServiceProvider implements ControllerProvider
{
    /**
     * Controllers to load in the application.
     *
     * @var array
     */
    protected $controllers = [];

    /**
     * @param \Igni\Application\HttpApplication|\Psr\Container\ContainerInterface $container
     */
    public function provideControllers($container): void
    {
        /** @var \Igni\Container\ServiceLocator $locator */
        $locator = $container->getContainer();

        foreach ($this->controllers as $controller) {
            $container->register($controller);
            $locator->bind($controller);
        }
    }
}
