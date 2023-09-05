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
 * InvalidCommandException
 */
class InvalidCommandException extends ConsoleException
{
    /**
     * Create a new InvalidCommandException.
     *
     * @param string|object $command
     * @param string $message
     * @param int $code
     * @param null|Throwable $previous
     */
    public function __construct(
        protected string|object $command,
        string $message = '',
        int $code = 0,
        null|Throwable $previous = null
    ) {
        if ($message === '') {
            $commandName = is_string($command) ? $command : $command::class;
            $message = sprintf('Invalid command %s', $commandName);
        }
        
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Returns the command.
     *
     * @return string|object
     */
    public function command(): string|object
    {
        return $this->command;
    }
}