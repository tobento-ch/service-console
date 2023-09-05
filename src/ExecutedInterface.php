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
 * ExecutedInterface
 */
interface ExecutedInterface
{
    /**
     * Returns the command.
     *
     * @return string
     */
    public function command(): string;
    
    /**
     * Returns the command.
     *
     * @return int
     */
    public function code(): int;
    
    /**
     * Returns the output.
     *
     * @return string
     */
    public function output(): string;
}