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
 * AbstractCommand
 */
abstract class AbstractCommand implements CommandInterface
{
    use HasParameters;
    
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;
    
    /**
     * The command name.
     */
    public const NAME = '';
    
    /**
     * The command description.
     */
    public const DESC = '';
    
    /**
     * The command usage text.
     */
    public const USAGE = '';
    
    /**
     * The name and signature of the console command.
     */
    public const SIGNATURE = '';
    
    /**
     * @var null|string
     */
    protected null|string $name = null;
    
    /**
     * @var null|string
     */
    protected null|string $description = null;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        if (!empty(static::SIGNATURE)) {
            [$this->name, $this->description] = SignatureParser::nameAndDesc(static::SIGNATURE);
        }
    }
    
    /**
     * Handle the command.
     *
     * @param InteractorInterface $io
     * @return int The exit status code: 
     *     0 SUCCESS
     *     1 FAILURE If some error happened during the execution
     *     2 INVALID To indicate incorrect command usage e.g. invalid options
     */
    abstract public function handle(InteractorInterface $io): int;
    
    /**
     * Returns the command name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?: static::NAME;
    }
    
    /**
     * Returns the command description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ?: static::DESC;
    }
    
    /**
     * Returns the command usage text.
     *
     * @return string
     */
    public function getUsage(): string
    {
        return static::USAGE;
    }
    
    /**
     * Returns the handler.
     *
     * @return callable
     */
    public function getHandler(): callable
    {
        return [$this, 'handle'];
    }
    
    /**
     * Returns the parameters.
     *
     * @return ParametersInterface
     */
    public function parameters(): ParametersInterface
    {
        if (is_null($this->parameters)) {
            if (!empty(static::SIGNATURE)) {
                $this->parameters = SignatureParser::parameters(static::SIGNATURE);
            } else {
                $this->parameters = new Parameters();
            }
        }
        
        return $this->parameters;
    }
}