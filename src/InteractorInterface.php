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

use Stringable;

/**
 * InteractorInterface
 */
interface InteractorInterface
{
    /**
     * Returns true if argument exists, otherwise false.
     *
     * @param string $name
     * @return bool
     */
    public function hasArgument(string $name): bool;
    
    /**
     * Returns the argument value by name.
     *
     * @param string $name
     * @return mixed
     */
    public function argument(string $name): mixed;
    
    /**
     * Returns all the arguments values indexed by its name.
     *
     * @return array
     */
    public function arguments(): array;

    /**
     * Returns true if option exists, otherwise false.
     *
     * @param string $name
     * @return bool
     */
    public function hasOption(string $name): bool;
    
    /**
     * Returns the option value by name.
     *
     * @param string $name
     * @return mixed
     */
    public function option(string $name): mixed;
    
    /**
     * Returns all the option values indexed by its name.
     *
     * @return array
     */
    public function options(): array;

    /**
     * Returns true if matches the specified verbosity level, otherwise false.
     *
     * @param string $level
     * @return bool
     */
    public function isVerbose(string $level): bool;
    
    /**
     * Write a message.
     *
     * @param string|Stringable $message
     * @param mixed ...$options
     * @return static $this
     */
    public function write(string|Stringable $message, mixed ...$options): static;
    
    /**
     * Write a blank new line.
     *
     * @param int $num The number of new lines.
     * @return static $this
     */
    public function newLine(int $num = 1): static;
    
    /**
     * Write an info message.
     *
     * @param string|Stringable $message
     * @param mixed ...$options
     * @return static $this
     */
    public function info(string|Stringable $message, mixed ...$options): static;
    
    /**
     * Write an comment message.
     *
     * @param string|Stringable $message
     * @param mixed ...$options
     * @return static $this
     */
    public function comment(string|Stringable $message, mixed ...$options): static;
    
    /**
     * Write a warning message.
     *
     * @param string|Stringable $message
     * @param mixed ...$options
     * @return static $this
     */
    public function warning(string|Stringable $message, mixed ...$options): static;
    
    /**
     * Write an error message.
     *
     * @param string|Stringable $message
     * @param mixed ...$options
     * @return static $this
     */
    public function error(string|Stringable $message, mixed ...$options): static;
    
    /**
     * Write a success message.
     *
     * @param string|Stringable $message
     * @param mixed ...$options
     * @return static $this
     */
    public function success(string|Stringable $message, mixed ...$options): static;
    
    /**
     * Writing a table.
     *
     * @param array $headers
     * @param array $rows
     * @return void
     */    
    public function table(array $headers, array $rows): void;
    
    /**
     * Asking a question returning its value.
     *
     * @param string $question
     * @param mixed $default
     * @param mixed ...$options
     * @return mixed
     */
    public function ask(string $question, mixed $default = null, mixed ...$options): mixed;
    
    /**
     * Asking a secret returning its value.
     *
     * @param string $question
     * @param mixed ...$options
     * @return mixed
     */
    public function secret(string $question, mixed ...$options): mixed;
    
    /**
     * Asking a confirm question returning its value.
     *
     * @param string $question
     * @param bool $default
     * @param mixed ...$options
     * @return mixed
     */
    public function confirm(string $question, bool $default = true, mixed ...$options): mixed;
    
    /**
     * Asking a choice question returning its value.
     *
     * @param string $question
     * @param array $choices
     * @param mixed $default
     * @param mixed ...$options
     * @return mixed
     */
    public function choice(string $question, array $choices, mixed $default = null, mixed ...$options): mixed;
    
    /**
     * Starts a progress.
     *
     * @param int $max
     * @return void
     */
    public function progressStart(int $max = 0): void;

    /**
     * Advances the progress started X steps.
     *
     * @param int $step
     * @return void
     */
    public function progressAdvance(int $step = 1): void;

    /**
     * Finishes the progress started.
     *
     * @return void
     */
    public function progressFinish(): void;
}