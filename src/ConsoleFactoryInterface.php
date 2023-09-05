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
 * ConsoleFactoryInterface
 */
interface ConsoleFactoryInterface
{
    /**
     * Create a new console based on the configuration.
     *
     * @param string $name
     * @param array $config
     * @return ConsoleInterface
     * @throws ConsoleException
     */
    public function createConsole(string $name, array $config = []): ConsoleInterface;
}