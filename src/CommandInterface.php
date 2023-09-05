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
 * CommandInterface
 */
interface CommandInterface
{
    /**
     * Returns the command name.
     *
     * @return string
     */
    public function getName(): string;
    
    /**
     * Returns the command description.
     *
     * @return string
     */
    public function getDescription(): string;
    
    /**
     * Returns the command usage text.
     *
     * @return string
     */
    public function getUsage(): string;
    
    /**
     * Returns the handler.
     *
     * @return callable
     */
    public function getHandler(): callable;
    
    /**
     * Returns the parameters.
     *
     * @return ParametersInterface
     */
    public function parameters(): ParametersInterface;
    
    /**
     * Add a parameter.
     *
     * @param ParameterInterface $parameter
     * @return static $this
     */
    public function parameter(ParameterInterface $parameter): static;
}