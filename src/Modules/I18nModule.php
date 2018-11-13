<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 01/11/2018
 */

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Http\MiddlewareAggregator;
use Igni\Application\Providers\MiddlewareProvider;
use Microparts\Configuration\Configuration;
use Microparts\I18n\I18nInterface;
use Microparts\I18n\Manager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class I18nModule implements MiddlewareProvider
{
    /**
     * @param \Igni\Application\Http\MiddlewareAggregator|\Igni\Application\HttpApplication $aggregate
     */
    public function provideMiddleware(MiddlewareAggregator $aggregate): void
    {
        /** @var \Igni\Container\ServiceLocator $container */
        $container = $aggregate->getContainer();

        $aggregate->use(function (ServerRequestInterface $request, callable $next) use ($container) {
            $container->share(I18nInterface::class, function (ContainerInterface $container) use ($request) {
                $manager = new Manager($container->get(Configuration::class));
                return $manager->withMessage($request)->load();
            });

            return $next($request);
        });
    }
}

