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
use Tobento\Service\Console\Input\Option;
use Tobento\Service\Console\ParameterInterface;

class OptionTest extends TestCase
{
    public function testInterfaceMethods()
    {
        $option = new Option(name: 'name');
        
        $this->assertInstanceof(ParameterInterface::class, $option);
        $this->assertSame(Option::class, $option->getName());
    }

    public function testMethodsDefaults()
    {
        $option = new Option(name: 'name');
        
        $this->assertSame('name', $option->name());
        $this->assertSame(null, $option->shortName());
        $this->assertSame('', $option->description());
        $this->assertSame(null, $option->variadic());
        $this->assertSame(null, $option->suggestedValues());
    }
    
    public function testMethods()
    {
        $option = new Option(
            name: 'name',
            shortName: 'n',
            description: 'desc',
            value: 'value',
            variadic: true,
            suggestedValues: ['foo'],
        );
        
        $this->assertSame('name', $option->name());
        $this->assertSame('n', $option->shortName());
        $this->assertSame('desc', $option->description());
        $this->assertSame(true, $option->variadic());
        $this->assertSame(['foo'], $option->suggestedValues());
    }
}