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

namespace Tobento\Service\Console\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Console\Command;
use Tobento\Service\Console\CommandInterface;
use Tobento\Service\Console\Parameters;
use Tobento\Service\Console\Input;

class CommandBaseTest extends TestCase
{
    public function testThatImplementsCommandInterface()
    {
        $this->assertInstanceof(CommandInterface::class, new Command(name: 'foo'));
    }
    
    public function testInterfaceMethods()
    {
        $command = new Command(name: 'foo');
        $command->parameter(new Input\Option(name: 'option'));
        
        $this->assertSame('foo', $command->getName());
        $this->assertSame('', $command->getDescription());
        $this->assertSame('', $command->getUsage());
        $this->assertTrue(is_callable($command->getHandler()));
        $this->assertSame(Input\Option::class, $command->parameters()->first()->getName());
    }
    
    public function testInterfaceMethodsWithAllParameters()
    {
        $command = new Command(
            name: 'foo',
            description: 'desc',
            usage: 'usage',
            parameters: new Parameters(
                new Input\Option(name: 'option'),
            ),
        );
        
        $this->assertSame('foo', $command->getName());
        $this->assertSame('desc', $command->getDescription());
        $this->assertSame('usage', $command->getUsage());
        $this->assertTrue(is_callable($command->getHandler()));
        $this->assertSame(Input\Option::class, $command->parameters()->first()->getName());
    }
    
    public function testDescAndUsageMethods()
    {
        $command = (new Command(name: 'foo'))
            ->description('desc')
            ->usage('usage');
        
        $this->assertSame('foo', $command->getName());
        $this->assertSame('desc', $command->getDescription());
        $this->assertSame('usage', $command->getUsage());
    }
    
    public function testHandleMethod()
    {
        $handler = function(): int {
            return 0;
        };
        
        $command = (new Command(name: 'foo'))
            ->handle($handler);
        
        $this->assertSame($handler, $command->getHandler());
    }
}