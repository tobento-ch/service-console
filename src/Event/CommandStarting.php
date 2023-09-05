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

namespace Tobento\Service\Console\Event;

use Tobento\Service\Console\CommandInterface;
use Tobento\Service\Console\InteractorInterface;

/**
 * CommandStarting
 */
final class CommandStarting
{
    /**
     * Create a new CommandStarting.
     *
     * @param CommandInterface $command
     * @param InteractorInterface $io
     */
    public function __construct(
        private CommandInterface $command,
        private InteractorInterface $io,
    ) {}
    
    /**
     * Returns the command.
     *
     * @return CommandInterface
     */
    public function command(): CommandInterface
    {
        return $this->command;
    }
    
    /**
     * Returns the interactor.
     *
     * @return InteractorInterface
     */
    public function io(): InteractorInterface
    {
        return $this->io;
    }
}