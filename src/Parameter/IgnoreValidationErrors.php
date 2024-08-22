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

namespace Tobento\Service\Console\Parameter;

use Tobento\Service\Console\ParameterInterface;

/**
 * Ignores validation errors. May be useful in some cases.
 */
class IgnoreValidationErrors implements ParameterInterface
{
    /**
     * Returns the parameter name.
     *
     * @return string
     */
    public function getName(): string
    {
        return static::class;
    }
}