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

use Closure;

/**
 * HasParameters
 */
trait HasParameters
{
    /**
     * @var null|ParametersInterface
     */
    protected null|ParametersInterface $parameters = null;
    
    /**
     * Returns the parameters.
     *
     * @return ParametersInterface
     */
    public function parameters(): ParametersInterface
    {
        if (is_null($this->parameters)) {
            $this->parameters = new Parameters();
        }
        
        return $this->parameters;
    }
    
    /**
     * Add a parameter.
     *
     * @param ParameterInterface $parameter
     * @return static $this
     */
    public function parameter(ParameterInterface $parameter): static
    {
        $this->parameters()->add($parameter);
        
        return $this;
    }
    
    /**
     * Add an input argument.
     *
     * @param $name
     * @param $description
     * @param $value
     * @param $optional
     * @param $variadic If true (e.g. <dir> [dirs...])
     * @param null|array|Closure $suggestedValues
     * @return static
     */
    public function argument(
        string $name,
        string $description = '',
        mixed $value = null,
        bool $optional = false,
        bool $variadic = false,
        null|array|Closure $suggestedValues = null,
    ): static {
        $this->parameter(new Input\Argument(
            name: $name,
            description: $description,
            value: $value,
            optional: $optional,
            variadic: $variadic,
            suggestedValues: $suggestedValues,
        ));
        
        return $this;
    }
    
    /**
     * Add an input option.
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
     * @return static
     */
    public function option(
        string $name,
        null|string $shortName = null,
        string $description = '',
        mixed $value = null,
        null|bool $variadic = null,
        null|array|Closure $suggestedValues = null,
    ): static {
        $this->parameter(new Input\Option(
            name: $name,
            shortName: $shortName,
            description: $description,
            value: $value,
            variadic: $variadic,
            suggestedValues: $suggestedValues,
        ));
        
        return $this;
    }
}