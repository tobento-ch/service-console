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
 * Option
 */
class Option implements ParameterInterface
{
    /**
     * Create a new Option.
     *
     * @param string $name
     * @param null|string $shortName
     * @param string $description
     * @param mixed $value
     * @param null|bool $variadic
     *     null: no value (--dir) if specified true, otherwise false.
     *     true: is variadic (e.g. --dir=foo --dir=bar).
     *     false: optional value (e.g. --dir or --dir=foo) if not specified default value is used.
     * @param null|array|Closure $suggestedValues
     */
    public function __construct(
        protected string $name,
        protected null|string $shortName = null,
        protected string $description = '',
        protected mixed $value = null,
        protected null|bool $variadic = null,
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
     * Returns the short name.
     *
     * @return null|string
     */
    public function shortName(): null|string
    {
        return $this->shortName;
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
     * Returns the variadic.
     *
     * @return null|bool
     *     null: no value (--dir) if specified true, otherwise false.
     *     true: is variadic (e.g. --dir=foo --dir=bar).
     *     false: optional value (e.g. --dir or --dir=foo) if not specified default value is used.
     */
    public function variadic(): null|bool
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