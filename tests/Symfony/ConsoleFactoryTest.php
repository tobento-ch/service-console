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

namespace Tobento\Service\Console\Test\Symfony;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Console\Symfony\ConsoleFactory;
use Tobento\Service\Console\ConsoleFactoryInterface;
use Tobento\Service\Container\Container;

class ConsoleFactoryTest extends TestCase
{
    public function testThatImplementsCommandInterface()
    {
        $factory = new ConsoleFactory(container: new Container());
        
        $this->assertInstanceof(ConsoleFactoryInterface::class, $factory);
    }
    
    public function testCreateConsoleMethod()
    {
        $factory = new ConsoleFactory(container: new Container());
        
        $this->assertSame('name', $factory->createConsole(name: 'name')->name());
    }
}