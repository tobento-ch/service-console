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

use Throwable;

/**
 * CommandNotFoundException
 */
class CommandNotFoundException extends ConsoleException
{
    /**
     * Create a new CommandNotFoundException.
     *
     * @param string $command
     * @param string $message
     * @param int $code
     * @param null|Throwable $previous
     */
    public function __construct(
        protected string $command,
        string $message = '',
        int $code = 0,
        null|Throwable $previous = null
    ) {
        if ($message === '') {
            $message = sprintf('Command %s not found', $command);    
        }
        
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Returns the command.
     *
     * @return string
     */
    public function command(): string
    {
        return $this->command;
    }
}