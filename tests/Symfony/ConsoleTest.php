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
use Tobento\Service\Console\Symfony\Console;
use Tobento\Service\Console\ConsoleInterface;
use Tobento\Service\Console\CommandInterface;
use Tobento\Service\Console\InteractorInterface;
use Tobento\Service\Console\Command;
use Tobento\Service\Console\Event;
use Tobento\Service\Console\ConsoleException;
use Tobento\Service\Console\InvalidCommandException;
use Tobento\Service\Console\CommandNotFoundException;
use Tobento\Service\Console\Test\Mock;
use Tobento\Service\Container\Container;
use Tobento\Service\Event\Events;
use Tobento\Service\Collection\Collection;
use Psr\EventDispatcher\EventDispatcherInterface;

class ConsoleTest extends TestCase
{
    public function testThatImplementsCommandInterface()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $this->assertInstanceof(ConsoleInterface::class, $console);
    }
    
    public function testAddCommandMethodThrowsExceptionIfNotExists()
    {
        $this->expectException(InvalidCommandException::class);
        
        $console = new Console(name: 'app', container: new Container());
        
        $console->addCommand('foo');
    }
    
    public function testHasCommandMethod()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $console->addCommand(Mock\Command::class);
        
        $this->assertTrue($console->hasCommand('command'));
        $this->assertFalse($console->hasCommand('foo'));
    }
    
    public function testGetCommandMethod()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $console->addCommand(Mock\Command::class);
        
        $this->assertInstanceof(CommandInterface::class, $console->getCommand('command'));
    }
    
    public function testGetCommandMethodThrowsExceptionIfNotExists()
    {
        $this->expectException(CommandNotFoundException::class);
        
        $console = new Console(name: 'app', container: new Container());
        
        $console->getCommand('command');
    }

    public function testExecuteMethodThrowsExceptionIfNotFound()
    {
        $this->expectException(ConsoleException::class);
        
        $console = new Console(name: 'app', container: new Container());
        
        $executed = $console->execute(command: 'command');
    }
    
    public function testExecuteMethodWithCommandClass()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $executed = $console->execute(command: Mock\Command::class, input: [
            'arg' => 'foo',
            '--opt' => 'bar',
        ]);
            
        $this->assertSame('command', $executed->command());
        $this->assertSame(0, $executed->code());
        $this->assertSame('arg:foo opt:bar', $executed->output());
    }
    
    public function testExecuteMethodWithCommandClassWithDependency()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $executed = $console->execute(command: Mock\CommandWithDependency::class, input: [
            'arg' => 'foo',
            '--opt' => 'bar',
        ]);
            
        $this->assertSame('command', $executed->command());
        $this->assertSame(0, $executed->code());
        $this->assertSame('arg:foo opt:bar', $executed->output());
    }
    
    public function testExecuteMethodWithCommandClassUsingSignature()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $executed = $console->execute(command: Mock\CommandSignature::class, input: [
            'arg' => 'foo',
            '--opt' => 'bar',
        ]);
            
        $this->assertSame('command:signature', $executed->command());
        $this->assertSame(0, $executed->code());
        $this->assertSame('arg:foo opt:bar', $executed->output());
    }
    
    public function testExecuteMethodWithCommand()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io, Mock\Foo $foo): int {
                $io->write($foo->name());
                return 0;
            });
        
        $executed = $console->execute(command: $command);
            
        $this->assertSame('name', $executed->command());
        $this->assertSame(0, $executed->code());
        $this->assertSame('foo', $executed->output());
    }
    
    public function testExecuteMethodWithCommandName()
    {
        $console = new Console(name: 'app', container: new Container());
        $console->addCommand(Mock\Command::class);
        
        $executed = $console->execute(command: 'command', input: [
            'arg' => 'foo',
            '--opt' => 'bar',
        ]);
            
        $this->assertSame('command', $executed->command());
        $this->assertSame(0, $executed->code());
        $this->assertSame('arg:foo opt:bar', $executed->output());
    }

    public function testArgumentOptional()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $command = (new Command(name: 'command'))
            ->argument(
                name: 'arg',
                optional: true,
            )
            ->handle(function(InteractorInterface $io): int {
                $value = $io->argument(name: 'arg');
                $io->write(gettype($value).':'.$value);
                return 0;
            });

        $executed = $console->execute(command: $command, input: []);
        $this->assertSame('NULL:', $executed->output());
        
        $executed = $console->execute(command: $command, input: [
            'arg' => 'foo',
        ]);
        $this->assertSame('string:foo', $executed->output());
    }
    
    public function testArgumentVariadicFalse()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $command = (new Command(name: 'command'))
            ->argument(
                name: 'arg',
                variadic: true,
            )
            ->handle(function(InteractorInterface $io): int {
                $value = $io->argument(name: 'arg');
                $io->write(gettype($value).':'.$value);
                return 0;
            });
        
        $executed = $console->execute(command: $command, input: ['arg' => 'foo']);
        $this->assertSame('string:foo', $executed->output());
    }
    
    public function testArgumentVariadicTrue()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $command = (new Command(name: 'command'))
            ->argument(
                name: 'arg',
                variadic: true,
            )
            ->handle(function(InteractorInterface $io): int {
                $value = $io->argument(name: 'arg');
                $io->write(gettype($value).':'.implode(',', $value));
                return 0;
            });
        
        $executed = $console->execute(command: $command, input: ['arg' => ['foo', 'bar']]);
        $this->assertSame('array:foo,bar', $executed->output());
    }
    
    public function testOptionVariadicNull()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $command = (new Command(name: 'command'))
            ->option(
                name: 'opt',
                variadic: null,
            )
            ->handle(function(InteractorInterface $io): int {
                $value = $io->option(name: 'opt');
                $io->write(gettype($value).':'.$value);
                return 0;
            });

        $executed = $console->execute(command: $command, input: []);
        $this->assertSame('boolean:', $executed->output());
        
        $executed = $console->execute(command: $command, input: ['--opt' => null]);
        $this->assertSame('boolean:1', $executed->output());
    }
    
    public function testOptionVariadicFalse()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $command = (new Command(name: 'command'))
            ->option(
                name: 'opt',
                variadic: false,
            )
            ->handle(function(InteractorInterface $io): int {
                $value = $io->option(name: 'opt');
                $io->write(gettype($value).':'.$value);
                return 0;
            });

        $executed = $console->execute(command: $command, input: []);
        $this->assertSame('NULL:', $executed->output());
        
        $executed = $console->execute(command: $command, input: ['--opt' => 'value']);
        $this->assertSame('string:value', $executed->output());
    }
    
    public function testOptionVariadicTrue()
    {
        $console = new Console(name: 'app', container: new Container());
        
        $command = (new Command(name: 'command'))
            ->option(
                name: 'opt',
                variadic: true,
            )
            ->handle(function(InteractorInterface $io): int {
                $value = $io->option(name: 'opt');
                $io->write(gettype($value).':'.implode(',', $value));
                return 0;
            });

        $executed = $console->execute(command: $command, input: []);
        $this->assertSame('array:', $executed->output());
        
        $executed = $console->execute(command: $command, input: ['--opt' => ['foo', 'bar']]);
        $this->assertSame('array:foo,bar', $executed->output());
    }    
    
    public function testEvents()
    {
        $events = new Events();
        $collection = new Collection();
        
        $events->listen(function(Event\CommandStarting $event) use ($collection) {
            $collection->add('started:command', $event->command());
        });
        
        $events->listen(function(Event\CommandFinished $event) use ($collection) {
            $collection->add('finished:command', $event->command());
        });
        
        $console = new Console(
            name: 'app',
            container: new Container(),
            eventDispatcher: $events,
        );
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io, Mock\Foo $foo): int {
                $io->write($foo->name());
                return 0;
            });
        
        $executed = $console->execute(command: $command);
        
        $this->assertTrue($command === $collection->get('started:command'));
        $this->assertTrue($command === $collection->get('finished:command'));
    }
}