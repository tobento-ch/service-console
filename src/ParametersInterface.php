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

use IteratorAggregate;

/**
 * ParametersInterface
 */
interface ParametersInterface extends IteratorAggregate
{
    /**
     * Add a new parameter.
     *
     * @param ParameterInterface $parameter
     * @return static $this
     */
    public function add(ParameterInterface $parameter): static;
    
    /**
     * Returns a new instance with the filtered parameters.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static;
    
    /**
     * Returns the parameter by name or null if not exists.
     *
     * @param string $name
     * @return null|ParameterInterface
     */
    public function get(string $name): null|ParameterInterface;
    
    /**
     * Returns a new instance with the filtered parameters by name.
     *
     * @param string $name
     * @return static
     */
    public function name(string $name): static;
    
    /**
     * Returns the first parameter of null if none.
     *
     * @return null|ParameterInterface
     */
    public function first(): null|ParameterInterface;
    
    /**
     * Returns the parameters.
     *
     * @return array<int, ParameterInterface>
     */
    public function all(): array;
}