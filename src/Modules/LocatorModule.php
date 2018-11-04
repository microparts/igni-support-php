<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 01/11/2018
 */

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Providers\ServiceProvider;
use Igni\Container\ServiceLocator;
use Psr\Container\ContainerInterface;

class LocatorModule implements ServiceProvider
{
    /**
     * @var \Igni\Container\ServiceLocator
     */
    private $locator;

    /**
     * LocatorModule constructor.
     *
     * @param \Igni\Container\ServiceLocator $locator
     */
    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param \Igni\Container\ServiceLocator|ContainerInterface $container
     */
    public function provideServices($container): void
    {
        $container->share(ServiceLocator::class, function () {
            return $this->locator;
        });
    }
}
