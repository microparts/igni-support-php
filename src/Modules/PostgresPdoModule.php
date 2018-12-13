<?php declare(strict_types=1);

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Providers\ServiceProvider;
use Illuminate\Container\Container;
use Microparts\Configuration\Configuration;
use Microparts\Configuration\ConfigurationInterface;
use PDO;
use Psr\Container\ContainerInterface;
use Roquie\Database\Connection\Wait\Wait;
use Roquie\Database\Migration\Migrate;
use Roquie\Database\Seed\Seed;

class PostgresPdoModule implements ServiceProvider
{
    /**
     * @param Container|ContainerInterface $container
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function provideServices($container): void
    {
        $conf = $container->get(Configuration::class);

        Wait::connection($this->buildDsn($conf), $this->migrate($conf, $container));
    }

    /**
     * @param \Microparts\Configuration\ConfigurationInterface $conf
     * @param ContainerInterface|Container $container
     * @return \Closure
     */
    private function migrate(ConfigurationInterface $conf, ContainerInterface $container)
    {
        return function ($pdo) use ($conf, $container) {
            if ($conf['db.migrate.auto']) {
                $this->apply($conf, $pdo);
            }

            if ($conf['db.seed.auto']) {
                Seed::new($pdo)->run();
            }

            $container->bind(PDO::class, function () use ($pdo, $conf) {
                $conn = $this->reconnectIfClosed($pdo, $conf);
                $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                return $conn;
            });
        };
    }

    /**
     * If database close connection, reconnect if you want.
     *
     * @param \PDO $pdo
     * @param \Microparts\Configuration\ConfigurationInterface $conf
     * @return \PDO
     */
    private function reconnectIfClosed(PDO $pdo, ConfigurationInterface $conf)
    {
        if ($pdo instanceof PDO) {
            return $pdo;
        }

        return new PDO($this->buildDsn($conf));
    }

    /**
     * @param \Microparts\Configuration\ConfigurationInterface $conf
     * @param $pdo
     */
    private function apply(ConfigurationInterface $conf, PDO $pdo): void
    {
        $migrate = Migrate::new($pdo);

        if ($conf['db.migrate.drop']) {
            $migrate->drop();
        }

        $migrate
            ->install()
            ->run();
    }

    /**
     * @param \Microparts\Configuration\ConfigurationInterface $conf
     * @return string
     */
    private function buildDsn(ConfigurationInterface $conf)
    {
        return "pgsql:dbname={$conf['db.name']};host={$conf['db.host']};user={$conf['db.user']};password={$conf['db.pwd']}";
    }
}
