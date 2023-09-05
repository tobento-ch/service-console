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

namespace Tobento\Service\Console\Input;

use Tobento\Service\Console\ParameterInterface;
use Closure;

/**
 * Argument
 */
class Argument implements ParameterInterface
{
    /**
     * Create a new Argument.
     *
     * @param string $name
     * @param string $description
     * @param mixed $value
     * @param bool $optional
     * @param bool $variadic If true (e.g. <dir> [dirs...])
     * @param null|array|Closure $suggestedValues
     */
    public function __construct(
        protected string $name,
        protected string $description = '',
        protected mixed $value = null,
        protected bool $optional = false,
        protected bool $variadic = false,
        protected null|array|Closure $suggestedValues = null,
    ) {}

    /**
     * Returns the parameter name.
     *
     * @return string
     */
    public function getName(): string
    {
        return static::class;
    }
    
    /**
     * Returns the option name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    /**
     * Returns the description.
     *
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }
    
    /**
     * Returns the value.
     *
     * @return mixed
     */
    public function value(): mixed
    {
        return $this->value;
    }

    /**
     * Returns true if argument is optional, otherwise false.
     *
     * @return bool
     */
    public function optional(): bool
    {
        return $this->optional;
    }
    
    /**
     * Returns the variadic.
     *
     * @return bool
     */
    public function variadic(): bool
    {
        return $this->variadic;
    }
    
    /**
     * Returns the suggested values.
     *
     * @return null|array|Closure
     */
    public function suggestedValues(): null|array|Closure
    {
        return $this->suggestedValues;
    }
}