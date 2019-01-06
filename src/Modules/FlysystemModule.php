<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 01/11/2018
 */

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Providers\ServiceProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Psr\Container\ContainerInterface;

class FlysystemModule implements ServiceProvider
{
    /**
     * @var bool
     */
    private $cache;

    /**
     * @var string
     */
    private $path;

    /**
     * FlysystemModule constructor.
     *
     * @param string $path
     * @param bool $cache
     */
    public function __construct(string $path = 'resource', bool $cache = true)
    {
        $this->path = $path;
        $this->cache = $cache;
    }

    /**
     * @param \Illuminate\Container\Container|ContainerInterface $container
     */
    public function provideServices($container): void
    {
        $container->singleton(FilesystemInterface::class, function () {
            $adapter = new Local($this->path);

            if ($this->cache) {
                $cache = new Memory();
                $adapter = new CachedAdapter($adapter, $cache);
            }

            return new Filesystem($adapter);
        });
    }
}
