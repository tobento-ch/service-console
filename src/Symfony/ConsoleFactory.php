<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Console\Symfony;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tobento\Service\Console\ConsoleFactoryInterface;
use Tobento\Service\Console\ConsoleInterface;
use Tobento\Service\Console\ConsoleException;

/**
 * ConsoleFactory
 */
class ConsoleFactory implements ConsoleFactoryInterface
{
    /**
     * Create a new ConsoleFactory.
     *
     * @param ContainerInterface $container
     * @param null|EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        protected ContainerInterface $container,
        protected null|EventDispatcherInterface $eventDispatcher = null,
    ) {}
    
    /**
     * Create a new console based on the configuration.
     *
     * @param string $name
     * @param array $config
     * @return ConsoleInterface
     * @throws ConsoleException
     */
    public function createConsole(string $name, array $config = []): ConsoleInterface
    {
        $container = $config['container'] ?? $this->container;
        $interactorFactory = $config['interactorFactory'] ?? null;
        $events = $config['events'] ?? true;
        
        return new Console(
            name: $name,
            container: $container,
            interactorFactory: $interactorFactory,
            eventDispatcher: $events ? $this->eventDispatcher : null,
        );
    }
}