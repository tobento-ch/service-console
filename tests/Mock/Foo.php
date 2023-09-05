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

namespace Tobento\Service\Console\Test\Mock;

final class Foo
{
    public function __construct(
        protected string $name = 'foo',
    ) {}
    
    public function name(): string
    {
        return $this->name;
    }
}