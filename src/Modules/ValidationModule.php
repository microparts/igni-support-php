<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 01/11/2018
 */

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Providers\ServiceProvider;
use Microparts\Igni\Support\Validation\Validator;
use PDO;
use Psr\Container\ContainerInterface;

class ValidationModule implements ServiceProvider
{
    /**
     * @param \Illuminate\Container\Container|ContainerInterface $container
     */
    public function provideServices($container): void
    {
        $container->bind(Validator::class, function (ContainerInterface $container) {
            return new Validator($container->get(PDO::class));
        });
    }
}
