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
use Psr\Http\Message\ServerRequestInterface;
use Microparts\Configuration\ConfigurationInterface;
use PDO;
use Roquie\Database\Connection\Wait\Wait;
use Roquie\Database\Migration\Migrate;
use Roquie\Database\Seed\Seed;

class PostgresPdoModule implements MiddlewareProvider
{
    /**
     * @param \Igni\Application\Http\MiddlewareAggregator|\Igni\Application\HttpApplication $aggregate
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function provideMiddleware(MiddlewareAggregator $aggregate): void
    {
        /** @var \Illuminate\Container\Container $container */
        $container = $aggregate->getContainer();
        $conf = $container->get(Configuration::class);

        Wait::connection($this->buildDsn($conf), $this->migrate($conf));

        $aggregate->use(function (ServerRequestInterface $request, callable $next) use ($conf, $container) {
            $conn = $this->createPdo($conf);

            $container->instance(PDO::class, $conn);
            $result = $next($request);

            // Close connection after request is executed.
            // SELECT * FROM pg_stat_activity WHERE pg_stat_activity.datname = '!!database_name' AND pid <> pg_backend_pid();
            $conn = null;
            $container->forgetInstance(PDO::class);

            return $result;
        });
    }

    /**
     * @param \Microparts\Configuration\ConfigurationInterface $conf
     * @return \Closure
     */
    private function migrate(ConfigurationInterface $conf)
    {
        return function ($pdo) use ($conf) {
            if ($conf['db.migrate.auto']) {
                $this->apply($conf, $pdo);
            }

            if ($conf['db.seed.auto']) {
                Seed::new($pdo)->run();
            }

            $pdo = null;
        };
    }

    /**
     * Create pdo-instance.
     *
     * @param \Microparts\Configuration\ConfigurationInterface $conf
     * @return \PDO
     */
    private function createPdo(ConfigurationInterface $conf)
    {
        $conn = new PDO($this->buildDsn($conf), null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        return $conn;
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
