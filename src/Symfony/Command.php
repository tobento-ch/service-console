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
use Tobento\Service\Console\CommandInterface;
use Tobento\Service\Console\Input\Argument;
use Tobento\Service\Console\Input\Option;
use Tobento\Service\Console\Event;
use Tobento\Service\Autowire\Autowire;
use Tobento\Service\Autowire\AutowireException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\LogicException;
use Closure;

/**
 * Command
 */
class Command extends SymfonyCommand
{
    /**
     * Create a new Command.
     *
     * @param ContainerInterface $container
     * @param CommandInterface $command
     * @param null|Closure $interactorFactory
     * @param null|EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        protected ContainerInterface $container,
        protected CommandInterface $command,
        protected null|Closure $interactorFactory = null,
        protected null|EventDispatcherInterface $eventDispatcher = null,
    ) {
        $this->setDescription($command->getDescription());
        
        parent::__construct($command->getName());
    }
    
    protected function configure(): void
    {        
        $this->mapArguments();
        $this->mapOptions();
        $this->setHelp($this->command->getUsage());
    }
    
    public function interactorFactory(Closure $factory): static
    {        
        $this->interactorFactory = $factory;
        return $this;
    }    

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @return int 0 if everything went fine, or an exit code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ToDo: check if command is locked
        
        if (!is_null($this->interactorFactory)) {
            $io = call_user_func_array($this->interactorFactory, [$this, $input, $output]);
        } else {
            $io = new Interactor($this, $input, $output);
        }
        
        $this->eventDispatcher?->dispatch(new Event\CommandStarting($this->command, $io));
        
        $code = (new Autowire($this->container))->call(
            $this->command->getHandler(),
            ['io' => $io],
        );
        
        $this->eventDispatcher?->dispatch(new Event\CommandFinished($this->command, $io));
        
        return $code;
    }

    /**
     * Map arguments.
     *
     * @return void
     */
    protected function mapArguments(): void
    {
        $arguments = $this->command->parameters()->name(Argument::class);
        
        foreach($arguments as $argument) {
            
            switch (true) {
                case $argument->variadic() && $argument->optional():
                    $mode = InputArgument::IS_ARRAY | InputArgument::OPTIONAL;
                    break;
                case $argument->variadic() && !$argument->optional():
                    $mode = InputArgument::IS_ARRAY | InputArgument::REQUIRED;
                    break;
                case $argument->optional():
                    $mode = InputArgument::OPTIONAL;
                    break;
                case ! $argument->optional():
                    $mode = InputArgument::REQUIRED;
                    break;
                default:
                    $mode = InputArgument::REQUIRED;
            }
            
            $this->addArgument(
                name: $argument->name(),
                mode: $mode,
                description: $argument->description(),
                default: $argument->value(),
                //suggestedValues: $argument->suggestedValues(),
            );
        }
    }
    
    /**
     * Map options.
     *
     * @return void
     */
    protected function mapOptions(): void
    {
        $options = $this->command->parameters()->name(Option::class);
        
        foreach($options as $option) {
            
            $mode = match ($option->variadic()) {
                null => InputOption::VALUE_NONE,
                true => InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                false => InputOption::VALUE_OPTIONAL,
            };
            
            $this->addOption(
                // this is the name that users must type to pass this option (e.g. --iterations=5)
                name: $option->name(),
                
                // this is the optional shortcut of the option name, which usually is just a letter
                // (e.g. `i`, so users pass it as `-i`); use it for commonly used options
                // or options with long names
                shortcut: $option->shortName(),
                
                // this is the type of option (e.g. requires a value, can be passed more than once, etc.)
                mode: $mode,
                
                // the option description displayed when showing the command help
                description: $option->description(),
                
                // the default value of the option (for those which allow to pass values)
                default: $option->value(),
                
                //suggestedValues: $option->suggestedValues(),
            );
        }
    }
}