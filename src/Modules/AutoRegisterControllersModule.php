<?php

namespace Microparts\Igni\Support\Modules;

use Igni\Application\ControllerAggregator;
use Igni\Application\Providers\ControllerProvider;
use Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class AutoRegisterControllersModule implements ControllerProvider
{
    /**
     * @var string
     */
    private $path;

    /**
     * AutoRegisterControllersModule constructor.
     *
     * @param string $path
     */
    public function __construct(string $path = 'app/Controllers')
    {
        $this->path = $path;
    }

    /**
     * @param \Igni\Application\ControllerAggregator|\Igni\Application\HttpApplication $aggregator
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function provideControllers(ControllerAggregator $aggregator): void
    {
        /** @var \Illuminate\Container\Container $container */
        $container = $aggregator->getContainer();

        /** @var LoggerInterface $logger */
        $logger = $container->get(LoggerInterface::class);

        $directory = new RecursiveDirectoryIterator($this->path);
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        $logger->debug(sprintf('Founded %s controllers. Start loading from path: %s.', iterator_count($regex), $this->path));

        foreach ($regex as $controller) {
            $class = substr(join('\\', array_map('ucfirst', explode('/', $controller[0]))), 0, -4);
            $aggregator->register($class);
            $container->singleton($class);
            $logger->debug(sprintf('Controller [%s] is registered and loaded.', $class));
        }
    }
}
