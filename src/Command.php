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

namespace Tobento\Service\Console;

/**
 * Command
 */
final class Command implements CommandInterface
{
    use HasParameters;
    
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;
    
    /**
     * @var null|callable
     */
    protected $handler = null;
    
    /**
     * Create a new Command.
     *
     * @param string $name
     * @param string $description
     * @param string $usage A usage (help) text.
     * @param null|ParametersInterface $parameters
     */
    public function __construct(
        protected string $name,
        protected string $description = '',
        protected string $usage = '',
        null|ParametersInterface $parameters = null,
    ) {
        $this->parameters = $parameters;
    }
    
    /**
     * Set a description.
     *
     * @param string $description
     * @return static $this
     */
    public function description(string $description): static
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Set a usage text.
     *
     * @param string $text
     * @return static $this
     */
    public function usage(string $text): static
    {
        $this->usage = $text;
        return $this;
    }
    
    /**
     * Handle the command.
     *
     * @param callable $handler
     * @return static $this
     */
    public function handle(callable $handler): static
    {
        $this->handler = $handler;
        return $this;
    }
    
    /**
     * Returns the command name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Returns the command description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
    
    /**
     * Returns the command usage text.
     *
     * @return string
     */
    public function getUsage(): string
    {
        return $this->usage;
    }
    
    /**
     * Returns the handler.
     *
     * @return callable
     */
    public function getHandler(): callable
    {
        if (is_callable($this->handler)) {
            return $this->handler;
        }
        
        return function(): int {
            return static::SUCCESS;
        };
    }
}