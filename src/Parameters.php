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

use ArrayIterator;
use Traversable;

/**
 * Parameters
 */
class Parameters implements ParametersInterface
{
    /**
     * @var array<int, ParameterInterface>
     */
    protected array $parameters = [];
    
    /**
     * Create a new Parameters.
     *
     * @param ParameterInterface ...$parameter
     */
    public function __construct(
        ParameterInterface ...$parameter,
    ) {
        $this->parameters = $parameter;
    }

    /**
     * Add a new parameter.
     *
     * @param ParameterInterface $parameter
     * @return static $this
     */
    public function add(ParameterInterface $parameter): static
    {
        $this->parameters[] = $parameter;
        
        return $this;
    }
    
    /**
     * Returns a new instance with the filtered parameters.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        $new = clone $this;
        $new->parameters = array_filter($this->parameters, $callback);
        return $new;
    }
    
    /**
     * Returns the parameter by name or null if not exists.
     *
     * @param string $name
     * @return null|ParameterInterface
     */
    public function get(string $name): null|ParameterInterface
    {
        return $this->name($name)->first();
    }
    
    /**
     * Returns a new instance with the filtered parameters by name.
     *
     * @param string $name
     * @return static
     */
    public function name(string $name): static
    {
        return $this->filter(
            fn(ParameterInterface $p) => $p->getName() === $name
        );
    }
    
    /**
     * Returns the first parameter of null if none.
     *
     * @return null|ParameterInterface
     */
    public function first(): null|ParameterInterface
    {
        $key = array_key_first($this->parameters);
        
        if (is_null($key)) {
            return null;
        }
        
        return $this->parameters[$key];    
    }
    
    /**
     * Returns the parameters.
     *
     * @return array<int, ParameterInterface>
     */
    public function all(): array
    {
        return $this->parameters;
    }
    
    /**
     * Get the iterator. 
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->all());
    }
}