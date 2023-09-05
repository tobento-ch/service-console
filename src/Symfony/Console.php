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
use Tobento\Service\Console\ConsoleInterface;
use Tobento\Service\Console\CommandInterface;
use Tobento\Service\Console\ExecutedInterface;
use Tobento\Service\Console\SignatureParser;
use Tobento\Service\Console\ConsoleException;
use Tobento\Service\Console\CommandNotFoundException;
use Tobento\Service\Console\InvalidCommandException;
use Tobento\Service\Autowire\Autowire;
use Tobento\Service\Autowire\AutowireException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\LazyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Closure;
use Throwable;

/**
 * Console
 */
class Console implements ConsoleInterface
{
    /**
     * @var array<string, string|CommandInterface>
     */
    protected array $commands = [];
    
    /**
     * @var null|Application
     */
    protected null|Application $app = null;
    
    /**
     * Create a new Console.
     *
     * @param string $name
     * @param ContainerInterface $container
     * @param null|Closure $interactorFactory
     * @param null|EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        protected string $name,
        protected ContainerInterface $container,
        protected null|Closure $interactorFactory = null,
        protected null|EventDispatcherInterface $eventDispatcher = null,
    ) {}
    
    /**
     * Returns the console name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Add a command.
     *
     * @param string|CommandInterface $command
     * @return static $this
     */
    public function addCommand(string|CommandInterface $command): static
    {
        if (
            is_string($command)
            && defined(sprintf('%s::%s', $command, 'SIGNATURE'))
            && !empty($command::SIGNATURE)
        ) {
            [$name, $description] = SignatureParser::nameAndDesc($command::SIGNATURE);
            
            $this->commands[$name] = $command;
            
            $this->app()->add(
                new LazyCommand(
                    name: $name,
                    aliases: [],
                    description: $description,
                    isHidden: false,
                    commandFactory: function () use ($command) {
                        $command = $this->createCommand($command);
                        return new Command($this->container, $command, $this->interactorFactory, $this->eventDispatcher);
                    },
                )
            );

            return $this;
        }
        
        if (
            is_string($command)
            && defined(sprintf('%s::%s', $command, 'NAME'))
            && !empty($command::NAME)
        ) {
            $this->commands[$command::NAME] = $command;
            
            $this->app()->add(
                new LazyCommand(
                    name: $command::NAME,
                    aliases: [],
                    description: defined(sprintf('%s::%s', $command, 'DESC')) ? $command::DESC : '',
                    isHidden: false,
                    commandFactory: function () use ($command) {
                        $command = $this->createCommand($command);
                        return new Command($this->container, $command, $this->interactorFactory, $this->eventDispatcher);
                    },
                )
            );

            return $this;
        }        
        
        if (is_string($command)) {
            $command = $this->createCommand($command);
        }
        
        $this->commands[$command->getName()] = $command;
        
        $this->app()->add(
            new LazyCommand(
                name: $command->getName(),
                aliases: [],
                description: $command->getDescription(),
                isHidden: false,
                commandFactory: function () use ($command) {
                    return new Command($this->container, $command, $this->interactorFactory, $this->eventDispatcher);
                },
            )
        );
        
        return $this;
    }

    /**
     * Returns true if command exists, otherwise false.
     *
     * @param string $name
     * @return bool
     */
    public function hasCommand(string $name): bool
    {
        return isset($this->commands[$name]);
    }
    
    /**
     * Returns the command or null if not exists.
     *
     * @param string $name The command name or classname.
     * @returnCommandInterface
     * @throws CommandNotFoundException
     */
    public function getCommand(string $name): CommandInterface
    {
        if (! $this->hasCommand($name)) {
            throw new CommandNotFoundException($name);
        }
        
        return $this->createCommand($this->commands[$name]);
    }
    
    /**
     * Create a command.
     *
     * @param string|CommandInterface $command
     * @return CommandInterface
     * @throws InvalidCommandException
     */
    public function createCommand(string|CommandInterface $command): CommandInterface
    {
        if (!is_string($command)) {
            return $command;
        }
        
        try {
            $command = (new Autowire($this->container))->resolve($command);
        } catch (AutowireException $e) {
            throw new InvalidCommandException($command, $e->getMessage(), (int)$e->getCode(), $e);
        }
        
        if ($command instanceof CommandInterface) {
            return $command;
        }
        
        throw new InvalidCommandException($command);
    }
    
    /**
     * Run console.
     *
     * @return int
     * @throws ConsoleException
     */
    public function run(): int
    {
        try {
            return $this->app()->run();
        } catch (Throwable $e) {
            throw new ConsoleException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }
    
    /**
     * Execute a command.
     *
     * @param string|CommandInterface $command A command name, classname or class instance.
     * @param array $input
     *   arguments: ['username' => 'Tom'] or ['username' => ['Tom', 'Tim']]
     *   options: ['--some-option' => 'value'] or ['--some-option' => ['value']]
     * @return ExecutedInterface
     * @throws ConsoleException
     */
    public function execute(string|CommandInterface $command, array $input = []): ExecutedInterface
    {
        if (is_string($command) && $this->hasCommand($command)) {
            $commandName = $command;
        } else {
            $this->addCommand($command);
            $commandName = $this->createCommand($command)->getName();
        }
        
        $input = array_merge(['command' => $commandName], $input);
        
        try {
            $output = new BufferedOutput();
            $code = $this->app()->run(new ArrayInput($input), $output);
            return new Executed(command: $commandName, code: $code, output: $output);
        } catch (Throwable $e) {
            throw new ConsoleException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }
    
    /**
     * Returns the symfony application.
     *
     * @return Application
     */    
    public function app(): Application
    {
        if (!is_null($this->app)) {
            return $this->app;
        }
        
        $this->app = new Application($this->name());
        $this->app->setCatchExceptions(false);
        $this->app->setAutoExit(false);
        
        return $this->app;
    }
}