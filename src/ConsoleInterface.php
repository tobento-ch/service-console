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
 * ConsoleInterface
 */
interface ConsoleInterface
{
    /**
     * Returns the console name.
     *
     * @return string
     */
    public function name(): string;
    
    /**
     * Add a command.
     *
     * @param string|CommandInterface $command
     * @return static $this
     */
    public function addCommand(string|CommandInterface $command): static;
    
    /**
     * Returns true if command exists, otherwise false.
     *
     * @param string $name
     * @return bool
     */
    public function hasCommand(string $name): bool;
    
    /**
     * Returns the command or null if not exists.
     *
     * @param string $name The command name or classname.
     * @returnCommandInterface
     * @throws CommandNotFoundException
     */
    public function getCommand(string $name): CommandInterface;
    
    /**
     * Run console.
     *
     * @return int
     * @throws ConsoleException
     */
    public function run(): int;
    
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
    public function execute(string|CommandInterface $command, array $input = []): ExecutedInterface;
}