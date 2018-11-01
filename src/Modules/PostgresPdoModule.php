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
use PDO;
use Psr\Container\ContainerInterface;
use Tmconsulting\Configuration;

class PostgresPdoModule implements ServiceProvider
{
    /**
     * @param ServiceLocator|ContainerInterface $container
     */
    public function provideServices($container): void
    {
        $container->share(PDO::class, function (ContainerInterface $container) {
            $conf = $container->get(Configuration::class);
            $dsn = "pgsql:dbname={$conf->get('db.name')};host={$conf->get('db.host')}";
            $pdo = new PDO($dsn, $conf->get('db.user'), $conf->get('db.pwd'));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        });
    }
}
