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

namespace Tobento\Service\Console\Test\Input;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Console\Input\Argument;
use Tobento\Service\Console\ParameterInterface;

class ArgumentTest extends TestCase
{
    public function testInterfaceMethods()
    {
        $argument = new Argument(name: 'name');
        
        $this->assertInstanceof(ParameterInterface::class, $argument);
        $this->assertSame(Argument::class, $argument->getName());
    }

    public function testMethodsDefaults()
    {
        $argument = new Argument(name: 'name');
        
        $this->assertSame('name', $argument->name());
        $this->assertSame('', $argument->description());
        $this->assertSame(false, $argument->optional());
        $this->assertSame(false, $argument->variadic());
        $this->assertSame(null, $argument->suggestedValues());
    }
    
    public function testMethods()
    {
        $argument = new Argument(
            name: 'name',
            description: 'desc',
            value: 'value',
            optional: true,
            variadic: true,
            suggestedValues: ['foo'],
        );
        
        $this->assertSame('name', $argument->name());
        $this->assertSame('desc', $argument->description());
        $this->assertSame(true, $argument->optional());
        $this->assertSame(true, $argument->variadic());
        $this->assertSame(['foo'], $argument->suggestedValues());
    }
}