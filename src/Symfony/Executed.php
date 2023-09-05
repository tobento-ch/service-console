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

use Tobento\Service\Console\ExecutedInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Executed
 */
class Executed implements ExecutedInterface
{
    /**
     * Create a new Executed.
     *
     * @param string $command
     * @param int $code
     * @param OutputInterface $output
     */
    public function __construct(
        protected string $command,
        protected int $code,
        protected OutputInterface $output,
    ) {}
    
    /**
     * Returns the command.
     *
     * @return string
     */
    public function command(): string
    {
        return $this->command;
    }
    
    /**
     * Returns the command.
     *
     * @return int
     */
    public function code(): int
    {
        return $this->code;
    }
    
    /**
     * Returns the output.
     *
     * @return string
     */
    public function output(): string
    {
        if ($this->output instanceof BufferedOutput) {
            return $this->output->fetch();
        }
        
        return '';
    }
}